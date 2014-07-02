<?php 
$I = new SeleniumTester($scenario);
$I->wantTo('check that user cannot login without verifying account');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'codeception@gmail.com');
$I->fillField('Password', 'codeception');
$I->click(['class' => 'btn']);
$I->see('Please click the link sent to your email');
$I->wait(3);
?>
