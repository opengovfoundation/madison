<?php

use Way\Tests\Assert;
use Way\Tests\Should;

class UserTest extends TestCase
{
    public function setUp(){
        parent::setUp();

        Eloquent::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();

        //Stub a generic user
        $this->user = $this->stubUser();
    }

    protected function stubUser(){
        $user = new User;
        $user->email = 'user@mymadison.io';
        $user->password = 'password';
        $user->fname = 'First';
        $user->lname = 'Last';

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

    public function test_user_saved_correctly(){
        $this->assertTrue($this->user->save());
        $this->assertTrue($this->user->exists);
    }

    public function test_hashes_password(){
        //TODO: Get the mock Hash class working ;)
        //Hash::shouldReceive('make')->once()->andReturn('hashed');

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