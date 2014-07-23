<?php 
/*
	Tests creating a group.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->createTestUser(true);
$I->wantTo('check that user can create a group');
TestCommons::loginTest($I);
// Request independent sponsor status through the docs page
$I->amOnPage('/groups/edit');
$I->fillField("gname", "Test group");
$I->fillField("dname", "Test group");
$I->fillField("address1", "1000 Liberty Blvd");
$I->fillField("address2", "Apt #1");
$I->fillField("city", 'Rockville');
$I->selectOption("state", 'Maryland');
$I->fillField("postal", "20852");
$I->fillField("phone", "301301301");
$I->click('Submit');
$I->see("Your group has been created!");
?>