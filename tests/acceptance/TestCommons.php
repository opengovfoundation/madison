<?php
class TestCommons
{
    public static $TEST_EMAIL = 'test@opengovfoundation.org';
    public static $TEST_PASSWORD = 'codeception';

    public static $ADMIN_EMAIL = 'admin@example.com';
    public static $ADMIN_PASSWORD = 'password';    

    public static function loginTest($I)
    {
    	$I->amOnPage('/user/login');
		$I->fillField('Email', self::$TEST_EMAIL);
		$I->fillField('Password', self::$TEST_PASSWORD);
		$I->click(['class' => 'btn']);
    }

    public static function loginAdmin($I)
    {
        $I->amOnPage('/user/login');
        $I->fillField('Email', self::$ADMIN_EMAIL);
        $I->fillField('Password', self::$ADMIN_PASSWORD);
		$I->click(['class' => 'btn']);
    }

}
?>