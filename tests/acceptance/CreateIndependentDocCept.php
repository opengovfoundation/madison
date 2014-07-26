<?php 
/*
	Tests creating a document as an independent sponsor.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->createTestUser(true);
$I->verifyTestIndependent();
TestCommons::loginTest($I);
$I->wantTo('check that an independent sponsor can create a document');
// Request independent sponsor status through the docs page
$I->amOnPage('/documents');
$I->see("No Documents Found");
$I->fillField("title", "Hello world");
$I->click("createdoc");
$I->see('Document Created Successfully');
?>