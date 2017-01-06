<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class SponsorMember extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $appends = ['name'];

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
        $user = User::where('id', '=', $this->user_id)->first();

        if (!$user) {
            throw new \Exception("Could not locate user with ID");
        }

        return "{$user->display_name}";
    }
}
