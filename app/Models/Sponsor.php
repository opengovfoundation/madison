<?php

namespace App\Models;

/**
 *  Sponsor Model.
 */
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SponsorMember;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

use Log;

class Sponsor extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public static $timestamp = true;
    protected $hidden = ['pivot', 'deleted_at', 'updated_at', 'created_at'];
    protected $fillable = [
        'name',
        'display_name',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'phone',
        'individual',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';

    const ROLE_OWNER = 'owner';
    const ROLE_EDITOR = 'editor';
    const ROLE_STAFF = 'staff';


    public static function getStatuses()
    {
        return array(static::STATUS_ACTIVE, static::STATUS_PENDING);
    }

    public static function isValidStatus($status)
    {
        switch ($status) {
            case static::STATUS_ACTIVE:
            case static::STATUS_PENDING:
                return true;
        }

        return false;
    }

    public static function isValidRole($role)
    {
        switch ($role) {
            case static::ROLE_EDITOR:
            case static::ROLE_OWNER:
            case static::ROLE_STAFF:
                return true;
        }

        return false;
    }

    public function docs()
    {
        return $this->belongsToMany('App\Models\Doc');
    }

    /**
     *  @todo is this used?  Used to be used in $this->userHasRole, but the logic there has been changed.
     */
    public function getRoleId($role)
    {
        $role = strtolower($role);

        if (!static::isValidRole($role)) {
            throw new \Exception("Invalid Role");
        }

        return "sponsor_{$this->id}_$role";
    }

    public function userHasRole($user, $role)
    {
        $sponsorMember = SponsorMember::where('sponsor_id', '=', $this->id)->where('user_id', '=', $user->id)->first();

        return $sponsorMember && $sponsorMember->role === $role;
    }

    public static function getRoles($forHtml = false)
    {
        if ($forHtml) {
            return [
                static::ROLE_OWNER => trans('messages.sponsor_member.roles.'.static::ROLE_OWNER),
                static::ROLE_EDITOR => trans('messages.sponsor_member.roles.'.static::ROLE_EDITOR),
                static::ROLE_STAFF => trans('messages.sponsor_member.roles.'.static::ROLE_STAFF),
            ];
        }

        return [static::ROLE_OWNER, static::ROLE_EDITOR, static::ROLE_STAFF];
    }

    protected function getPermissionsArray()
    {
        return [
            [
                'name' => "sponsor_{$this->id}_create_document",
                'display_name' => "Create Documents",
            ],
            [
                'name' => "sponsor_{$this->id}_edit_document",
                'display_name' => 'Edit Documents',
            ],
            [
                'name' => "sponsor_{$this->id}_delete_document",
                'display_name' => "Delete Documents",
            ],
            [
                'name' => "sponsor_{$this->id}_manage_document",
                'display_name' => "Manage Documents",
            ],
        ];
    }

    public function createRbacRules()
    {
        $this->destroyRbacRules();

        $ownerRole = new Role();
        $ownerRole->name = "sponsor_{$this->id}_owner";
        $ownerRole->save();

        $permissions = $this->getPermissionsArray();

        $permIds = array();

        $permLookup = array();

        foreach ($permissions as $perm) {
            $permModel = new Permission();

            foreach ($perm as $key => $val) {
                $permModel->$key = $val;
            }

            $permModel->save();

            $permIds[] = $permModel->id;

            switch ($perm['name']) {
                case "sponsor_{$this->id}_create_document":
                    $permLookup['create'] = $permModel->id;
                    break;
                case "sponsor_{$this->id}_edit_document":
                    $permLookup['edit'] = $permModel->id;
                    break;
                case "sponsor_{$this->id}_delete_document":
                    $permLookup['delete'] = $permModel->id;
                    break;
                case "sponsor_{$this->id}_manage_document":
                    $permLookup['manage'] = $permModel->id;
                    break;
            }
        }

        $ownerRole->perms()->sync($permIds);

        $editorRole = new Role();
        $editorRole->name = "sponsor_{$this->id}_editor";
        $editorRole->save();

        $editorRole->perms()->sync(array(
            $permLookup['create'],
            $permLookup['edit'],
            $permLookup['manage'],
        ));

        $staffRole = new Role();
        $staffRole->name = "sponsor_{$this->id}_staff";
        $staffRole->save();

        $users = array(
            static::ROLE_OWNER => $this->findUsersByRole(static::ROLE_OWNER),
            static::ROLE_EDITOR => $this->findUsersByRole(static::ROLE_EDITOR),
            static::ROLE_STAFF => $this->findUsersByRole(static::ROLE_STAFF),
        );

        foreach ($users as $role => $userList) {
            foreach ($userList as $userObj) {
                switch ($role) {
                    case static::ROLE_OWNER:
                        $userObj->attachRole($ownerRole);
                        break;
                    case static::ROLE_EDITOR:
                        $userObj->attachRole($editorRole);
                        break;
                    case static::ROLE_STAFF:
                        $userObj->attachRole($staffRole);
                        break;
                }
            }
        }
    }

    public function destroyRbacRules()
    {
        $permissions = $this->getPermissionsArray();

        $members = SponsorMember::where('sponsor_id', '=', $this->id)->get();

        $roles = Role::where('name', '=', "sponsor_{$this->id}_owner")
                     ->orWhere('name', '=', "sponsor_{$this->id}_editor")
                     ->orWhere('name', '=', "sponsor_{$this->id}_staff")
                     ->get();

        foreach ($roles as $role) {
            foreach ($members as $member) {
                $user = User::where('id', '=', $member->user_id)->first();
                $user->detachRole($role);
            }
            if ($role instanceof Role) {
                $role->delete();
            }
        }

        foreach ($permissions as $permData) {
            $perm = Permission::where('name', '=', $permData['name'])->first();

            if ($perm instanceof Permission) {
                $perm->delete();
            }
        }
    }

    public function members()
    {
        return $this->hasMany('App\Models\SponsorMember');
    }

    public static function findByUserId($userId, $onlyActive = true)
    {
        $sponsorMember = static::join('sponsor_members', 'sponsors.id', '=', 'sponsor_members.sponsor_id')
                             ->where('sponsor_members.user_id', '=', $userId);

        if ($onlyActive) {
            $sponsorMember->where('sponsors.status', '=', static::STATUS_ACTIVE);
        }

        return $sponsorMember->get(array(
            'sponsors.id', 'sponsors.name', 'sponsors.address1',
            'sponsors.address2', 'sponsors.city', 'sponsors.state',
            'sponsors.postal_code', 'sponsors.phone', 'sponsors.display_name',
            'sponsors.status', 'sponsors.created_at', 'sponsors.updated_at',
            'sponsors.deleted_at', ));
    }

    public static function findByMemberId($memberId)
    {
        $sponsorMember = SponsorMember::where('id', '=', $memberId)->first();

        if (!$sponsorMember) {
            return;
        }

        return static::where('id', '=', $sponsorMember->sponsor_id)->first();
    }

    public function hasMember($user_id)
    {
        return static::isValidUserForSponsor($user_id, $this->id);
    }

    public static function isValidUserForSponsor($user_id, $sponsor_id)
    {
        $sponsor = static::where('id', '=', $sponsor_id)->first();

        if (!$sponsor) {
            throw new \Exception("Invalid Sponsor ID $sponsor_id");
            return false;
        }

        $member = $sponsor->findMemberByUserId($user_id);

        return !!$member;
    }

    public function findUsersByRole($role)
    {
        if (!static::isValidRole($role)) {
            return;
        }

        $members = SponsorMember::where('role', '=', $role)
            ->where('sponsor_id', '=', $this->id)->get();

        $retval = new Collection();
        foreach ($members as $member) {
            $userModel = User::where('id', '=', $member->user_id)->first();

            if ($userModel) {
                $retval->add($userModel);
            }
        }

        return $retval;
    }

    public function findMemberByUserId($userId)
    {
        if (!isset($this->id) || empty($this->id)) {
            throw new \Exception("You must have a sponsor ID set in order to search for members");
        }

        return SponsorMember::where('user_id', '=', $userId)->where('sponsor_id', '=', $this->id)->first();
    }

    public function isSponsorOwner($userId)
    {
        $sponsorMember = $this->findMemberByUserId($userId);

        return $sponsorMember && $sponsorMember->role == static::ROLE_OWNER;
    }

    public function getMemberRole($userId)
    {
        $sponsorMember = $this->findMemberByUserId($userId);

        return $sponsorMember->role;
    }

    public function addMember($userId, $role = null)
    {
        $sponsorMember = $this->findMemberByUserId($userId);

        if (!$sponsorMember) {
            if (is_null($role)) {
                throw new \Exception("You must provide a role if adding a new member");
            }

            if (!isset($this->id) || empty($this->id)) {
                throw new \Exception("The sponsor must have a ID set in order to add a member");
            }

            $sponsorMember = new SponsorMember();
            $sponsorMember->user_id = $userId;
            $sponsorMember->role = $role;
            $sponsorMember->sponsor_id = $this->id;
        } else {
            if (!is_null($role)) {
                $sponsorMember->role = $role;
            }
        }

        $sponsorMember->save();

        return $sponsorMember;
    }

    public function userCanCreateDocument($user)
    {
        return $this->userHasRole($user, Sponsor::ROLE_EDITOR) || $this->userHasRole($user, Sponsor::ROLE_OWNER);
    }

    public function isActive()
    {
        return $this->status === static::STATUS_ACTIVE;
    }
}
