<?php 
/*
	Tests creating a group through a regular
	account, and approving group through an
	admin account.
    TODO: Test managing members of group
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->createTestUser();
$I->wantTo('check that user can create a group and have it be verified');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'test@opengovfoundation.org');
$I->fillField('Password', 'codeception');
$I->click('Login', ['class' => 'btn']);
// Create group
$I->amOnPage('/groups');
$I->see('You are not the member of any groups');
$I->amOnPage('/groups/edit');
$I->fillField("gname", 'Test Group');
$I->fillField("dname", 'Test Group');
$I->fillField("address1", "1000 Liberty Blvd");
$I->fillField("address2", "Apt #1");
$I->fillField("city", 'Rockville');
$I->selectOption("state", 'Maryland');
$I->fillField("postal", "20852");
$I->fillField("phone", "301301301");
$I->click('Submit', ['class' => 'btn']);
$I->see("Your group has been created! It must be approved before you can invite others to join or create documents");
// Using the default SQL dump, the test user would be the second record after the admin seed, 
// so user_id = 2
$I->seeInDatabase('group_members', ['id' => 1, 'user_id' => 2]);
$I->seeInDatabase('groups', ['id' => 1, 'status' => 'pending']);
// Verify with admin account
$admin = $I->haveFriend('admin');
$admin->does(function(AcceptanceTester $I) {
	// These admin values are taken from the SQL dump
	$I->amOnPage('/user/login');
    $I->fillField('Email', 'admin@example.com');
    $I->fillField('Password', 'password');
    $I->click('Login', ['class' => 'btn']);
    $I->see("You have been successfully logged in");
    $I->sendAjaxPostRequest('/api/groups/verify/', array('request' => array('id' => 1), 'status' => 'active'));
});
// Check verification in DB and interface
$I->seeInDatabase('groups', ['id' => 1, 'status' => 'active']);
$I->amOnPage('/groups');
$I->see("active");