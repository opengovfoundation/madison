<?php 
/*
	Tests requesting verified status through a regular
	account, and approving verified status through an
	admin account.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database
$I->createTestUser();
$I->wantTo('check that user can request a verified account');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'test@opengovfoundation.org');
$I->fillField('Password', 'codeception');
$I->click('Login', ['class' => 'btn']);
// Open profile page and request verification
$userid = $I->grabFromDatabase('users', 'id', array('email' => 'test@opengovfoundation.org'));
$I->amOnPage('/user/edit/' . $userid);
$I->see("Request 'Verified Account'");
$I->checkOption("verify");
$I->click('Submit', ['class' => 'btn']);
$I->see("Your verified status has been requested");
$I->seeInDatabase('user_meta', ['user_id' => $userid, 'meta_value' => 'pending']);
// Verify with admin account
$admin = $I->haveFriend('admin');
$admin->does(function(AcceptanceTester $I) {
	// These admin values are taken from the SQL dump
	$I->amOnPage('/user/login');
    $I->fillField('Email', 'admin@example.com');
    $I->fillField('Password', 'password');
    $I->click('Login', ['class' => 'btn']);
    $I->see("You have been successfully logged in");
    // id = 1 refers to the ID of the user_meta request, there should only be 1 if using
    // a clean SQL dump
    $I->sendAjaxPostRequest('/api/user/verify/', array('request' => array('id' => 1), 'status' => 'verified'));
});
// Check verification in DB and interface
$I->seeInDatabase('user_meta', ['user_id' => $userid, 'meta_value' => 'verified']);
$I->amOnPage('/user/edit/' . $userid);
$I->see("Request 'Verified Account' is 'verified'");
?>
