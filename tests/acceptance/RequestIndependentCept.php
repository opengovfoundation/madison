<?php 
/*
	Tests requesting individual sponsor status through
    a regular account, and approving it through an admin
    account.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->createTestUser(true);
$I->wantTo('check that user can request independent sponsor status');
TestCommons::loginTest($I);
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
$I->click('Submit');
$I->see("Your request has been received");
?>