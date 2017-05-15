<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LoginToken extends Model
{
    protected $fillable = ['token', 'expires_at'];
    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($token) {
            if (empty($token->token)) {
                $token->token = str_random(20);
            }

            if (empty($token->expires_at)) {
                $token->expires_at = Carbon::now()->addDay();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
