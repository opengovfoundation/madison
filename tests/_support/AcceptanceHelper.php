<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AcceptanceHelper extends \Codeception\Module
{
	function createTestUser() {
		// Insert test user into database
		$dbh = $this->getModule('Db');
		$dbh->haveInDatabase('users', 
							 array('email' => 'test@opengovfoundation.org', 
						     	   'password' => '$2y$10$sESIh1sRtuINotOAPXsjOeVSXQ8wpW/vi4yLnnunKTTrkCfRpIi3W', 
						           'fname' => 'Codeception', 
							       'lname' => 'McIntire', 
							       'token' => '')
                            );
	}
}