<?php

namespace Madison\Services\Validators;

class User extends Validator{

  /**
  * Validation Rules
  */
  public static $rules = array(
    'email'     => 'required|unique:users',
    'password'  => 'required',
    'fname'     => 'required',
    'lname'     => 'required'
  );
}