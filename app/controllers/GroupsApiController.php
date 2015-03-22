<?php

class GroupsApiController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    
        $this->beforeFilter('auth', array('on' => array('post', 'put', 'delete')));
    }
    
    public function getVerify()
    {
        $this->beforeFilter('admin');
        
        $groups = Group::all();
        
        return Response::json($groups);
    }
    
    public function postVerify()
    {
        $this->beforeFilter('admin');
        
        $request = Input::get('request');
        $status = Input::get('status');
        
        if (!Group::isValidStatus($status)) {
            throw new \Exception("Invalid value for verify request");
        }
        
        $group = Group::where('id', '=', $request['id'])->first();
        
        if (!$group) {
            throw new \Exception("Invalid Group");
        }
        
        $group->status = $status;
        
        DB::transaction(function () use ($group) {
            $group->save();
            
            switch ($group->status) {
                case Group::STATUS_ACTIVE:
                    $group->createRbacRules();
                    break;
                case Group::STATUS_PENDING:
                    $group->destroyRbacRules();
                    break;
            }
        });
        
        return Response::json($group);
    }
}
