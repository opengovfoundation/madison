<?php
/*
	Tests signing in a regular user through the
	click interface.
*/
$I = new AcceptanceTester($scenario);
$I->createTestUser(false);
$I->wantTo('check that user cannot login if not verified');

// Check unverified login	
TestCommons::loginTest($I);
$I->see('Please click the link sent to your email');

?>