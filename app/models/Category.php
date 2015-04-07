<?php
/**
 * 	Document meta model.
 */
class Category extends Eloquent
{
    //Document this meta is describing
    public function docs()
    {
        return $this->belongsToMany('Doc');
    }
}
