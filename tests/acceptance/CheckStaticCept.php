<?php 
/*
	Tests static pages.
*/
$I = new AcceptanceTester($scenario);
// Create user record in database and login
$I->wantTo('check static pages');
// Request independent sponsor status through the docs page
$I->amOnPage('/about');
$I->see("Madison is a government policy co-creation platform");
$I->amOnPage('/faq');
$I->see("Madison is a free tool that helps elected officials");
?>