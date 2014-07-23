<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class FunctionalHelper extends \Codeception\Module
{
  function createAdminUser() {
    $db = $this->getModule('Db');
    $id = $db->haveInDatabase('users',
                        array(
                          'email'     => 'test@opengovfoundation.org',
                          'password'  => '$2y$10$sESIh1sRtuINotOAPXsjOeVSXQ8wpW/vi4yLnnunKTTrkCfRpIi3W',
                          'fname'     => 'Codeception',
                          'lname'     => 'McIntire',
                        )
    );

    $db->haveInDatabase('assigned_roles',
                        array(
                          'user_id' => $id,
                          'role_id' => 1
                        );
    );

    return $id;
  }
}