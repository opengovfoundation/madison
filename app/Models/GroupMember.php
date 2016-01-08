<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class GroupMember extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public static $timestamp = true;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public static function findByGroupId($groupId)
    {
        return static::where('group_id', '=', $groupId)->get();
    }

    public function getUserName()
    {
        $user = User::where('id', '=', $this->user_id)->first();

        if (!$user) {
            throw new \Exception("Could not locate user with ID");
        }

        return "{$user->fname} {$user->lname}";
    }
}
