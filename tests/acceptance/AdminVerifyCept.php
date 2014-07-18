<?php 
/*
	Tests requesting verified status through a regular
	account, and approving verified status through an
	admin account.
*/
$I = new AcceptanceTester($scenario);
$I->wantTo('check that admin can verify users');
$I->createTestUser(true);
$userid = $I->grabFromDatabase('users', 'id', array('email' => 'test@opengovfoundation.org'));
$I->createVerifyRequest($userid);
TestCommons::loginAdmin($I);
$I->see("You have been successfully logged in");
$I->amOnPage('/dashboard/verifications');
$I->click(['class' => 'btn-success']);
$I->seeInDatabase('user_meta', array('user_id' => $userid, 'meta_key' => 'verify', 'meta_value' => 'verified'));
?>