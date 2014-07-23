<?php 
/*
	Tests adding a group member.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->createTestUser(true);
$I->createTestGroup();
$I->wantTo('check that user can add group members');
TestCommons::loginTest($I);
// Request independent sponsor status through the docs page
$I->amOnPage('/groups/invite/1');
$I->fillField("email", "admin@example.com");
$I->click('Submit');
$I->see("User added successfully!");
?>