<?php
/*
	Tests signing in a regular user through the
	click interface.
*/
$I = new AcceptanceTester($scenario);
$I->createTestUser(true);
$I->wantTo('check that user can login if verified');

// Check verified login
TestCommons::loginTest($I);
$I->see('Welcome Codeception');

?>