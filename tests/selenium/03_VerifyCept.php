<?php 
$I = new SeleniumTester($scenario);
$I->wantTo('check that user can verify account');
$I->amOnPage('/');
$token = $I->grabFromDatabase('users', 'token', array('email' => 'codeception@gmail.com'));
$I->amOnPage('/user/verify/' . $token);
$I->see('Your email has been verified and you have been logged in.');
?>
