<?php 
/*
	Tests requesting verified status through a regular
	account, and approving verified status through an
	admin account.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->createTestUser(true);
$I->wantTo('check that user can request a verified account and be verified');
TestCommons::loginTest($I);
// Open profile page and request verification
$userid = $I->grabFromDatabase('users', 'id', array('email' => 'test@opengovfoundation.org'));
$I->amOnPage('/user/edit/' . $userid);
$I->see("Request 'Verified Account'");
$I->checkOption("#verify");
$I->click(['class' => 'btn']);
$I->see("Your verified status has been requested");
// Verify with admin account
$admin = $I->haveFriend('admin');
$admin->does(function(AcceptanceTester $I) {
    TestCommons::loginAdmin($I);
    $I->see("You have been successfully logged in");
    $I->amOnPage('/dashboard/verifications');
    $I->click(['class' => 'btn-success']);
});
$I->amOnPage('/user/edit/' . $userid);
$I->see("Request 'Verified Account' is 'verified'");
?>
