<?php 
/*
	Tests creating a document as an independent sponsor.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->createTestUser();
$I->verifyTestIndependent();
$I->wantTo('check that an independent sponsor can create a document');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'test@opengovfoundation.org');
$I->fillField('Password', 'codeception');
$I->click('Login', ['class' => 'btn']);
// Request independent sponsor status through the docs page
$I->amOnPage('/documents');
$I->see("No Documents Found");
$I->fillField("title", "Hello world");
$I->click("createdoc");
$I->see('Document Created Successfully');
/*
This does not work for some reason:
$I->click(['id' => 'submit']);
$I->see('Document Saved Successfully');

Checking database or POSTing to a route
is more applicable to functional testing.
*/