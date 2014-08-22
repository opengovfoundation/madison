<?php

use Way\Tests\Assert;
use Way\Tests\Should;

class UserTest extends TestCase
{
    use ModelHelpers;

    protected $user;

    public function setUp(){
        parent::setUp();

        Eloquent::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();

        //Stub a generic user
        $this->user = $this->stubUser();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function stubUser(){
        $user = new User;
        $user->email = 'user@mymadison.io';
        $user->password = 'password';
        $user->fname = 'First';
        $user->lname = 'Last';

        return $user;
    }

    protected function stubOAuthUser(){
        $user = $this->stubUser();
        unset($user->password);
        $user->oauth_vendor = 'facebook';
        $user->oauth_id = '11111111111111111';
        $user->oauth_update = 1;

        return $user;
    }

    protected function stubTwitterUser(){
        $user = $this->stubUser();
        unset($user->email);
        unset($user->password);
        $user->oauth_vendor = 'twitter';
        $user->oauth_id = '11111111';
        $user->oauth_update = 1;
        
        return $user;
    }

    public function test_fname_is_required() {
        unset($this->user->fname);
        
        $this->assertFalse($this->user->save());

        $errors = $this->user->getErrors()->all();

        //Ensure we only have the error we expect
        $this->assertCount(1, $errors);

        //Check the string value for that error
        $this->assertEquals($errors[0], "The first name field is required.");
    }

    public function test_lname_is_required() {
        unset($this->user->lname);

        $this->assertFalse($this->user->save());

        $errors = $this->user->getErrors()->all();
        $this->assertCount(1, $errors);

        $this->assertEquals($errors[0], "The last name field is required.");
    }

    public function test_signup_rules_set_correctly(){
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

    public function test_update_rules_set_correctly(){
        $this->user->save();
        $rules = $this->user->mergeRules();

        $expected_rules = array(
            'fname'     => 'required',
            'lname'     => 'required',
            'email'     => 'required|unique:users,email,' . $this->user->id,
            'password'  => 'required'
        );

        $this->assertEquals($expected_rules, $rules);
    }

    public function test_social_login_rules_set_correctly(){
        $oauth_user = $this->stubOAuthUser();
        $rules = $oauth_user->mergeRules();

        $expected_rules = array(
            'fname'         => 'required',
            'lname'         => 'required',
            'email'         => 'required|unique:users',
            'oauth_vendor'  => 'required',
            'oauth_id'      => 'required',
            'oauth_update'  => 'required'
        );
        
        $this->assertEquals($expected_rules, $rules);
    }

    public function test_twitter_login_rules_set_correctly(){
        $twitter_user = $this->stubTwitterUser();
        $rules = $twitter_user->mergeRules();

        $expected_rules = array(
            'fname' => 'required',
            'lname' => 'required',
            'oauth_vendor'  => 'required',
            'oauth_id'      => 'required',
            'oauth_update'  => 'required'
        );

        $this->assertEquals($expected_rules, $rules);
    }

    public function test_email_must_be_unique(){
        $this->user->save();

        $dupe = new User;
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

    public function test_user_saved_correctly(){
        $this->assertTrue($this->user->save());
        $this->assertTrue($this->user->exists);
    }

    public function test_hashes_password(){
        //Test that the password gets hashed
        $this->assertNotEquals($this->user->password, 'password');
    }

    public function test_twitter_signup_saves() {
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

    public function test_facebook_signup_saves(){
        unset($this->user->password);

        $this->user->oauth_vendor = 'facebook';
        $this->user->oauth_id = '11111111111111111';
        $this->user->oauth_update = 1;

        $this->assertTrue($this->user->save());
        $this->assertTrue($this->user->exists); 
    }

}