<?php

use Way\Tests\Assert;
use Way\Tests\Should;

class UserTest extends TestCase
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
        
    }

    protected function _after()
    {

    }

    public function test_fname_is_required() {
        $user = new User;
        $user->email = 'user@mymadison.io';
        $user->password = 'password';
        $user->lname = 'User';
        $this->assertFalse($user->save());

        $errors = $user->getErrors()->all();
        $this->assertCount(1, $errors);

        $this->assertEquals($errors[0], "The first name field is required.");
    }

    public function test_lname_is_required() {
        $user = new User;
        $user->email = 'user@mymadison.io';
        $user->password = 'password';
        $user->fname = 'User';
        $this->assertFalse($user->save());

        $errors = $user->getErrors()->all();
        $this->assertCount(1, $errors);

        $this->assertEquals($errors[0], "The last name field is required.");
    }

    public function test_user_saved_correctly(){
        $user = new User;
        $user->email = 'user@mymadison.io';
        $user->password = 'password';
        $user->fname = 'First';
        $user->lname = 'Last';

        $this->assertTrue($user->save());
        $this->assertTrue($user->exists);

        //Test that the password gets hashed
        $this->assertNotEquals($user->password, 'password');
    }

    public function test_twitter_signup_saves() {
        $user = new User;
        $user->fname = "Fname Lname";
        $user->lname = "-";
        $user->oauth_vendor = 'twitter';
        $user->oauth_id = '11111111';
        $user->oauth_update = 1;

        $this->assertTrue($user->save(), "Save returned false");
        $this->assertTrue($user->exists, "Exists returned false");
    }

    public function test_facebook_signup_saves(){
        $user = new User;
        $user->fname = "Fname";
        $user->lname = "Lname";
        $user->email = "user@mymadison.io";
        $user->oauth_vendor = 'facebook';
        $user->oauth_id = '11111111111111111';
        $user->oauth_update = 1;

        $this->assertTrue($user->save());
        $this->assertTrue($user->exists); 
    }

}