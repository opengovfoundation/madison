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

	function verifyTestIndependent() {
		// Instead of relying on the test user to have a user id of 2
		// all the time, let's grab the ID first
		$dbh = $this->getModule('Db');
		$userid = $dbh->grabFromDatabase('users', 'id', array('email' => 'test@opengovfoundation.org'));
		$dbh->haveInDatabase('user_meta', 
							 array('user_id' => $userid, 
						     	   'meta_key' => 'independent_author', 
						           'meta_value' => '1')
                            );		
		$dbh->haveInDatabase('assigned_roles', 
							 array('user_id' => $userid, 
						     	   'role_id' => 2)
                            );	
	}
}