<?php 
$I = new SeleniumTester($scenario);
$I->haveInDatabase('users', array('email' => 'codeception@gmail.com', 
								  'password' => '$2y$10$sESIh1sRtuINotOAPXsjOeVSXQ8wpW/vi4yLnnunKTTrkCfRpIi3W', 
								  'fname' => 'Codeception', 
								  'lname' => 'McIntire', 
								  'token' => '7IU1u49GhrBsY5oP')
                  );
$I->wantTo('check that user cannot login without verifying account');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'codeception@gmail.com');
$I->fillField('Password', 'codeception');
$I->click(['class' => 'btn']);
$I->see('Please click the link sent to your email');
?>
