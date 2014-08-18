<?php

namespace Madison\Services\Validators;

abstract class Validator{

  protected $input;

  protected $errors;

  /**
  * Constructor
  *
  * @param array $input (optional)
  *   Optional because we may just want to use Input::all()
  */
  public function __construct($input = NULL){
    $this->input = $input ?: \Input::all();
  }


  /**
  * Passes
  *
  * @param void
  * @return boolean
  * 
  * Creates new instance of the Validator class.
  *   Checks validation and returns true
  *   Otherwise sets $errors property and returns false
  */
  public function passes(){
    $validation = \Validator::make($this->input, $static::$rules);

    if($validation->passes()){
      return true;
    }

    $this->errors = $validation->messages();

    return false;
  }

  /**
  * getErrors
  *
  * @param void
  * @return array $errors
  */
  public function getErrors(){
    return $this->errors;
  }
}