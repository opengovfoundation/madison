<?php 
$I = new SeleniumTester($scenario);
$I->haveInDatabase('users', array('email' => 'codeception@gmail.com', 
								  'password' => '$2y$10$sESIh1sRtuINotOAPXsjOeVSXQ8wpW/vi4yLnnunKTTrkCfRpIi3W', 
								  'fname' => 'Codeception', 
								  'lname' => 'McIntire', 
								  'token' => '7IU1u49GhrBsY5oP')
                  );
$I->wantTo('check that user can verify account');
$I->amOnPage('/');
$token = $I->grabFromDatabase('users', 'token', array('email' => 'codeception@gmail.com'));
$I->amOnPage('/user/verify/' . $token);
$I->see('Your email has been verified and you have been logged in.');
?>
