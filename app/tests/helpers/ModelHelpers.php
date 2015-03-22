<?php

trait ModelHelpers
{
  public function assertValid($model)
  {
      $this->assertTrue(
      $model->validate(),
      'Model did not pass validation.'
    );
  }

    public function assertNotValid($model)
    {
        $this->assertFalse(
       $model->validate().
       'Did not expect model to pass validation.'
    );
    }
}
