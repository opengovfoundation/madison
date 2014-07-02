<?php 
$I = new SeleniumTester($scenario);
$I->wantTo('register a new user');
$I->amOnPage('/');
$I->click('Sign Up');
$I->fillField("First Name", 'Codeception');
$I->fillField("Last Name", 'McIntire');
$I->fillField("Email", "codeception@gmail.com");
$I->fillField("Password", "codeception");
$I->click(['class' => 'btn']);
$I->seeInDatabase('users', ['email' => 'codeception@gmail.com']);
$I->see("An email has been sent to your email address.");
$I->wait(3);
?>
