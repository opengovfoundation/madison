<?php 
$I = new AcceptanceTester($scenario);
$I->haveInDatabase('users', array('email' => 'test@opengovfoundation.org', 
								  'password' => '$2y$10$sESIh1sRtuINotOAPXsjOeVSXQ8wpW/vi4yLnnunKTTrkCfRpIi3W', 
								  'fname' => 'Codeception', 
								  'lname' => 'McIntire', 
								  'token' => '')
                  );
$I->wantTo('check that user can request a verified account');
$I->amOnPage('/');
$I->amOnPage('/user/login');
$I->fillField('Email', 'test@opengovfoundation.org');
$I->fillField('Password', 'codeception');
$I->click('Login', ['class' => 'btn']);
$userid = $I->grabFromDatabase('users', 'id', array('email' => 'test@opengovfoundation.org'));
$I->amOnPage('/user/edit/' . $userid);
$I->see("Request 'Verified Account'");
$I->checkOption("verify");
$I->click('Submit', ['class' => 'btn']);
$I->see("Your verified status has been requested");
$I->seeInDatabase('user_meta', ['user_id' => $userid, 'meta_value' => 'pending']);
?>
