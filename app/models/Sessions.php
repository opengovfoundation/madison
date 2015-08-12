<?php

/**
 *  Session table model.  It should be named 'Session',
 *  but we've already got an alias of that name.
 */
class Sessions extends Eloquent
{
    protected $table = 'sessions';
    public $timestamps = false;

    public function getPayloadAttribute()
    {
        return json_decode($this->attributes['payload'], true);
    }

    public function setPayloadAttribute($data)
    {
        $this->attributes['payload'] = json_encode($data);
    }
}
