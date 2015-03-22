<?php

class GroupMember extends Eloquent
{
    public static $timestamp = true;
    protected $softDelete = true;
    
    public function user()
    {
        return $this->belongsTo('User');
    }
    
    public function group()
    {
        return $this->belongsTo('Group');
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
