<?php
/*
	Tests signing in a regular user through the
	click interface.
*/
$I = new AcceptanceTester($scenario);
$I->createTestUser();
$I->wantTo('check that user can login normally');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'test@opengovfoundation.org');
$I->fillField('Password', 'codeception');
$I->click('Login', ['class' => 'btn']);
$I->see('Welcome Codeception');
?>
