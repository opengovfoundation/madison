<?php

class UserController
{
  protected $user;

  public function __construct(WebGuy $I) {
    $this->user = $I;
  }

  public function login($username, $password) {
    $this->user->amOnPage(LoginPage::$URL);
    $this->user->fillField(LoginPage::$usernameField, $username);
    $this->user->fillField(LoginPage::$passwordField, $password);
    $this->user->click(LoginPage::$submitButton);
    $this->user->see('You have been successfully logged in.');
  }
}