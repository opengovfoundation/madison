<?php
namespace Codeception\Module;

/*
	Helpers for acceptance testing. Note that many of these functions
	assume there are no groups, documents, etc. in the database, the test
	is running from a clean seed with only one admin user (there is some
	flexibility built into this, as the test user id is grabbed from the db
	instead of assumed in some tests, but this needs to be expanded).
*/

class AcceptanceHelper extends \Codeception\Module
{

	// Create a test user
	// $verified - should the new test user be verified (token field empty) or not, true/false boolean
	function createTestUser($verified) {
		$dbh = $this->getModule('Db');
		$dbh->haveInDatabase('users', 
							 array('email' => 'test@opengovfoundation.org', 
						     	   'password' => '$2y$10$sESIh1sRtuINotOAPXsjOeVSXQ8wpW/vi4yLnnunKTTrkCfRpIi3W',
						     	   'phone' => '555-555-5555', 
						           'fname' => 'Codeception', 
							       'lname' => 'McIntire', 
							       'token' => $verified == true ? '' : '7IU1u49GhrBsY5oP')
                            );
	}

	function createVerifyRequest($userid) {
		$dbh = $this->getModule('Db');
		$dbh->haveInDatabase('user_meta', 
							 array('user_id' => $userid, 
							 	   'meta_key' => 'verify',
						     	   'meta_value' => 'pending')
                            );
	}	

	// Give test user independent author status. Run createTestUser() first.
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

	// Create a test group that is verified and owner by the test user created by createTestUser().
	function createTestGroup() {
		$dbh = $this->getModule('Db');
		$userid = $dbh->grabFromDatabase('users', 'id', array('email' => 'test@opengovfoundation.org'));
		$dbh->haveInDatabase('groups', 
							 array('name' => 'Test Group', 
						     	   'address1' => 'Test address line 1', 
						           'address2' => 'Test address line 2',
							 	   'city' => 'Miami', 
						           'state' => 'FL',
								   'postal_code' => '20852', 
						           'phone_number' => '5555555555', 
						           'display_name' => 'Test Group',
						           'status' => 'active',
						           )
                            );
		// Assuming there is only one group, per clean SQL dump
		$dbh->haveInDatabase('group_members', 
							 array('group_id' => 1, 
							 	   'user_id' => $userid, 
						     	   'role' => 'owner')
                            );

	}

	// Create a test document owned by the test user created by createTestUser().
	function createTestDocument() {
		$dbh = $this->getModule('Db');
		$userid = $dbh->grabFromDatabase('users', 'id', array('email' => 'test@opengovfoundation.org'));
		$dbh->haveInDatabase('docs', 
							 array('title' => "Hello world!", 
							 	   'slug' => "hello-world!", 
						     	   'init-section' => '1',
						     	   )
                            );
		$dbh->haveInDatabase('doc_user', 
							 array('doc_id' => 1, 
							 	   'user_id' => $userid)
                            );		
		$dbh->haveInDatabase('doc_contents', 
							 array('doc_id' => 1, 
							 	   'child_priority' => '0', 
						     	   'content' => 'This is a test document, created by an automatic helper function.')
                            );

	}
}
