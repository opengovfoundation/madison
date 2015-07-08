<?php


class UserTest extends TestCase
{
    use ModelHelpers;

    protected $user;

    /**
     *   setUp.
     *
     *   Runs before each test
     *       Stubs $this->user
     *       Truncates User table
     */
    public function setUp()
    {
        parent::setUp();

        Eloquent::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();

        //Stub a generic user
        $this->user = $this->stubUser();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     *   stubUser.
     *
     *   Helper function to stub a generic User
     *
     *   @param void
     *
     *   @return User $user
     */
    protected function stubUser()
    {
        $user = new User();
        $user->email = 'user@mymadison.io';
        $user->password = 'password';
        $user->fname = 'First';
        $user->lname = 'Last';

        return $user;
    }

    /**
     *   stubOauthUser.
     *
     *   Helper function to stub a Facebook Oauth User
     *
     *   @param void
     *
     *   @return User $user
     */
    protected function stubOAuthUser()
    {
        $user = $this->stubUser();
        unset($user->password);
        $user->oauth_vendor = 'facebook';
        $user->oauth_id = '11111111111111111';
        $user->oauth_update = 1;

        return $user;
    }

    /**
     *   stubTwitterUser.
     *
     *   Helper function to stub a Twitter OAuth User
     *
     *   @param void
     *
     *   @return User $user
     */
    protected function stubTwitterUser()
    {
        $user = $this->stubUser();
        unset($user->email);
        unset($user->password);
        $user->oauth_vendor = 'twitter';
        $user->oauth_id = '11111111';
        $user->oauth_update = 1;

        return $user;
    }

    /**
     *   @test
     */
    public function fname_is_required()
    {
        unset($this->user->fname);

        $this->assertFalse($this->user->save());

        $errors = $this->user->getErrors()->all();

        //Ensure we only have the error we expect
        $this->assertCount(1, $errors);

        //Check the string value for that error
        $this->assertEquals($errors[0], "The first name field is required.");
    }

    /**
     *   @test
     */
    public function lname_is_required()
    {
        unset($this->user->lname);

        $this->assertFalse($this->user->save());

        $errors = $this->user->getErrors()->all();
        $this->assertCount(1, $errors);

        $this->assertEquals($errors[0], "The last name field is required.");
    }

    /**
     *   @test
     */
    public function signup_rules_set_correctly()
    {
        $rules = $this->user->mergeRules();

        $this->assertArrayHasKey('fname', $rules);
        $this->assertArrayHasKey('lname', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);

        $this->assertEquals($rules['fname'], 'required');
        $this->assertEquals($rules['lname'], 'required');
        $this->assertEquals($rules['email'], 'required|unique:users');
        $this->assertEquals($rules['password'], 'required');
    }

    /**
     *   @test
     */
    public function update_rules_set_correctly()
    {
        $this->user->save();
        $rules = $this->user->mergeRules();

        $expected_rules = array(
            'fname'     => 'required',
            'lname'     => 'required',
            'email'     => 'required|unique:users,email,'.$this->user->id,
            'password'  => 'required',
        );

        $this->assertEquals($expected_rules, $rules);
    }

    /**
     *   @test
     */
    public function social_login_rules_set_correctly()
    {
        $oauth_user = $this->stubOAuthUser();
        $rules = $oauth_user->mergeRules();

        $expected_rules = array(
            'fname'         => 'required',
            'lname'         => 'required',
            'email'         => 'required|unique:users',
            'oauth_vendor'  => 'required',
            'oauth_id'      => 'required',
            'oauth_update'  => 'required',
        );

        $this->assertEquals($expected_rules, $rules);
    }

    /**
     *   @test
     */
    public function twitter_login_rules_set_correctly()
    {
        $twitter_user = $this->stubTwitterUser();
        $rules = $twitter_user->mergeRules();

        $expected_rules = array(
            'fname' => 'required',
            'lname' => 'required',
            'oauth_vendor'  => 'required',
            'oauth_id'      => 'required',
            'oauth_update'  => 'required',
        );

        $this->assertEquals($expected_rules, $rules);
    }

    /**
     *   @test
     */
    public function email_must_be_unique()
    {
        $this->user->save();

        $dupe = new User();
        $dupe->fname = "Some";
        $dupe->lname = "Name";
        $dupe->email = "user@mymadison.io";
        $dupe->password = "something";

        $dupe->rules = $dupe->mergeRules();
        $this->assertFalse($dupe->validate());

        $errors = $dupe->getErrors()->all();

        $this->assertCount(1, $errors);

        $this->assertEquals($errors[0], "The email has already been taken.");
    }

    /**
     *   @test
     */
    public function user_saved_correctly()
    {
        $this->assertTrue($this->user->save());
        $this->assertTrue($this->user->exists);
    }

    /**
     *   @test
     */
    public function hashes_password()
    {
        //Test that the password gets hashed
        $this->assertNotEquals($this->user->password, 'password');
    }

    /**
     *   @test
     */
    public function display_name()
    {
        $this->assertEquals('First Last', $this->user->getDisplayName());
    }

    /**
     *   @test
     */
    public function twitter_signup_saves()
    {
        unset($this->user->email);
        unset($this->user->password);

        $this->user->fname = "Fname Lname";
        $this->user->lname = "-";
        $this->user->oauth_vendor = 'twitter';
        $this->user->oauth_id = '11111111';
        $this->user->oauth_update = 1;

        $this->assertTrue($this->user->save(), "Save returned false");
        $this->assertTrue($this->user->exists, "Exists returned false");
    }

    /**
     *   @test
     */
    public function facebook_signup_saves()
    {
        unset($this->user->password);

        $this->user->oauth_vendor = 'facebook';
        $this->user->oauth_id = '11111111111111111';
        $this->user->oauth_update = 1;

        $this->assertTrue($this->user->save());
        $this->assertTrue($this->user->exists);
    }
}
