<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class SponsorMember extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $appends = ['name', 'email'];

    public static $timestamp = true;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function sponsor()
    {
        return $this->belongsTo('App\Models\Sponsor');
    }

    public static function findBySponsorId($sponsorId)
    {
        return static::where('sponsor_id', '=', $sponsorId)->get();
    }

    public function getNameAttribute()
    {
        return "{$this->user->display_name}";
    }

    public function getEmailAttribute()
    {
        return "{$this->user->email}";
    }
}
