<?php 
$I = new AcceptanceTester($scenario);
$I->haveInDatabase('users', array('email' => 'test@opengovfoundation.org', 
								  'password' => '$2y$10$sESIh1sRtuINotOAPXsjOeVSXQ8wpW/vi4yLnnunKTTrkCfRpIi3W', 
								  'fname' => 'Codeception', 
								  'lname' => 'McIntire', 
								  'token' => '7IU1u49GhrBsY5oP')
                  );
$I->wantTo('check that user cannot login without verifying account');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'test@opengovfoundation.org');
$I->fillField('Password', 'codeception');
$I->click('Login', ['class' => 'btn']);
$I->see('Please click the link sent to your email');
?>
