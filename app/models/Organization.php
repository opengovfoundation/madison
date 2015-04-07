<?php
/**
 * 	Organization Model.
 */
class Organization extends Eloquent
{
    public static $timestamp = true;
    protected $softDelete = true;

    //Users belonging to this organization
    public function users()
    {
        return $this->has_many('User');
    }
}
