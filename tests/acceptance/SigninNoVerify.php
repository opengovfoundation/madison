<?php 
/*
	Tests signing in a regular user without verifying
	the email, checking to see that the proper error
	is triggered.
*/
$I = new AcceptanceTester($scenario);
$I->createTestUser();
$I->wantTo('check that user cannot login without verifying account');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'test@opengovfoundation.org');
$I->fillField('Password', 'codeception');
$I->click('Login', ['class' => 'btn']);
$I->see('Please click the link sent to your email');
?>
