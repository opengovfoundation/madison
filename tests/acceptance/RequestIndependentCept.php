<?php 
/*
	Tests requesting individual sponsor status through
    a regular account, and approving it through an admin
    account.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->createTestUser();
$I->wantTo('check that user can request independent sponsor status and have it be granted');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'test@opengovfoundation.org');
$I->fillField('Password', 'codeception');
$I->click('Login', ['class' => 'btn']);
// Request independent sponsor status through the docs page
$I->amOnPage('/documents');
$I->see('Want to be a document sponsor?');
$I->amOnPage('/documents/sponsor/request');
$I->fillField("address1", "1000 Liberty Blvd");
$I->fillField("address2", "Apt #1");
$I->fillField("city", 'Rockville');
$I->selectOption("state", 'Maryland');
$I->fillField("postal", "20852");
$I->fillField("phone", "301301301");
$I->click('Submit', ['class' => 'btn']);
$I->see("Your request has been received");
$I->seeInDatabase('user_meta', ['id' => 1, 'meta_key' => 'independent_author']);
// Verify with admin account
$admin = $I->haveFriend('admin');
$admin->does(function(AcceptanceTester $I) {
	// These admin values are taken from the SQL dump
    $I->amOnPage('/user/login');
    $I->fillField('Email', 'admin@example.com');
    $I->fillField('Password', 'password');
    $I->click('Login', ['class' => 'btn']);
    $I->see("You have been successfully logged in");
    $I->sendAjaxPostRequest('/api/user/independent/verify', array('request' => array('user' => array('id' => 2)), 'status' => 'verified'));
});
// Check verification in DB and interface
$I->seeInDatabase('user_meta', ['id' => 1, 'meta_key' => 'independent_author', 'meta_value' => 1]);
$I->amOnPage('/documents');
$I->see("No Documents Found");
