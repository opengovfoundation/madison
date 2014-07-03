<?php 
$I = new SeleniumTester($scenario);
$I->wantTo('register a new user');
$I->amOnPage('/');
$I->click('Sign Up');
$I->fillField("First Name", 'Codeception');
$I->fillField("Last Name", 'McIntire');
$I->fillField("Email", "test@opengovfoundation.org");
$I->fillField("Password", "codeception");
$I->click(['class' => 'btn']);
$I->seeInDatabase('users', ['email' => 'test@opengovfoundation.org']);
$I->see("An email has been sent to your email address.");
?>
