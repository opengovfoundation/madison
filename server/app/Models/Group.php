<?php

namespace App\Models;

/**
 *	Group Model.
 */
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\GroupMember;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

use Log;

class Group extends Model
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
        'user_id'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';

    const ROLE_OWNER = 'owner';
    const ROLE_EDITOR = 'editor';
    const ROLE_STAFF = 'staff';

    /**
     *	Validation Rules.
     */
    public static $rules = array(
        'name' => 'required',
        'address1' => 'required',
        'city' => 'required',
        'state' => 'required',
        'postal_code' => 'required',
        'phone' => 'required',
        'display_name' => 'required',
    );

    protected static $customMessages = array(
      'name.required'                    => 'The group name is required',
      'address1.required'            => 'The group address is required',
      'city.required'                    => 'The group city is required',
      'state.required'                => 'The group state is required',
      'postal_code.required'    => 'The group postal code is required',
      'phone.required'    => 'The group phone number is required',
      'display_name.required'    => 'The group display name is required',
    );

    /**
     *	Constructor.
     *
     *	@param array $attributes
     *	Extends Eloquent constructor
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->validationErrors = new MessageBag();
    }

    /**
     *	Save.
     *
     *	Override Eloquent save() method
     *		Runs $this->beforeSave()
     *		Unsets:
     *			* $this->validationErrors
     *			* $this->rules
     *
     *	@param array $options
     *	$return bool
     */
    public function save(array $options = array())
    {
        if (!$this->beforeSave()) {
            return false;
        }

        //Don't want Group model trying to save validationErrors field.
        unset($this->validationErrors);

        return parent::save($options);
    }

    /**
     *	getErrors.
     *
     *	Returns errors from validation
     *
     *	@param void
     *
     *	@return MessageBag $this->validationErrors
     */
    public function getErrors()
    {
        return $this->validationErrors;
    }

    /**
     *	beforeSave.
     *
     *	Validates before saving.  Returns whether the Group can be saved.
     *
     *	@param array $options
     *
     *	@return bool
     */
    private function beforeSave(array $options = array())
    {
        if (!$this->validate()) {
            Log::error("Unable to validate group: ");
            Log::error($this->getErrors()->toArray());
            Log::error($this->attributes);

            return false;
        }

        return true;
    }

    /**
     *	Validate.
     *
     *	Validate input against merged rules
     *
     *	@param array $attributes
     *
     *	@return bool
     */
    public function validate()
    {
        $validation = Validator::make($this->attributes, static::$rules, static::$customMessages);

        if ($validation->passes()) {
            return true;
        }

        $this->validationErrors = $validation->messages();

        return false;
    }

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

    public function getDisplayName()
    {
        return !empty($this->display_name) ? $this->display_name : !empty($this->name) ? $this->name : "";
    }

    /**
     *	@todo is this used?  Used to be used in $this->userHasRole, but the logic there has been changed.
     */
    public function getRoleId($role)
    {
        $role = strtolower($role);

        if (!static::isValidRole($role)) {
            throw new \Exception("Invalid Role");
        }

        return "group_{$this->id}_$role";
    }

    public function userHasRole($user, $role)
    {
        $groupMember = GroupMember::where('group_id', '=', $this->id)->where('user_id', '=', $user->id)->first();

        return $groupMember && $groupMember->role === $role;
    }

    public static function getRoles($forHtml = false)
    {
        if ($forHtml) {
            return array(
                static::ROLE_OWNER => static::ROLE_OWNER,
                static::ROLE_EDITOR => static::ROLE_EDITOR,
                static::ROLE_STAFF => static::ROLE_STAFF,
            );
        }

        return array(static::ROLE_OWNER, static::ROLE_EDITOR, static::ROLE_STAFF);
    }

    protected function getPermissionsArray()
    {
        return array(
            array(
                'name' => "group_{$this->id}_create_document",
                'display_name' => "Create Documents",
            ),
            array(
                'name' => "group_{$this->id}_edit_document",
                'display_name' => 'Edit Documents',
            ),
            array(
                'name' => "group_{$this->id}_delete_document",
                'display_name' => "Delete Documents",
            ),
            array(
                'name' => "group_{$this->id}_manage_document",
                'display_name' => "Manage Documents",
            ),
        );
    }

    public function createRbacRules()
    {
        $this->destroyRbacRules();

        $ownerRole = new Role();
        $ownerRole->name = "group_{$this->id}_owner";
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
                case "group_{$this->id}_create_document":
                    $permLookup['create'] = $permModel->id;
                    break;
                case "group_{$this->id}_edit_document":
                    $permLookup['edit'] = $permModel->id;
                    break;
                case "group_{$this->id}_delete_document":
                    $permLookup['delete'] = $permModel->id;
                    break;
                case "group_{$this->id}_manage_document":
                    $permLookup['manage'] = $permModel->id;
                    break;
            }
        }

        $ownerRole->perms()->sync($permIds);

        $editorRole = new Role();
        $editorRole->name = "group_{$this->id}_editor";
        $editorRole->save();

        $editorRole->perms()->sync(array(
            $permLookup['create'],
            $permLookup['edit'],
            $permLookup['manage'],
        ));

        $staffRole = new Role();
        $staffRole->name = "group_{$this->id}_staff";
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

        $members = GroupMember::where('group_id', '=', $this->id)->get();

        $roles = Role::where('name', '=', "group_{$this->id}_owner")
                     ->orWhere('name', '=', "group_{$this->id}_editor")
                     ->orWhere('name', '=', "group_{$this->id}_staff")
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
        return $this->hasMany('App\Models\GroupMember');
    }

    public static function findByUserId($userId, $onlyActive = true)
    {
        $groupMember = static::join('group_members', 'groups.id', '=', 'group_members.group_id')
                             ->where('group_members.user_id', '=', $userId);

        if ($onlyActive) {
            $groupMember->where('groups.status', '=', static::STATUS_ACTIVE);
        }

        return $groupMember->get(array(
            'groups.id', 'groups.name', 'groups.address1',
            'groups.address2', 'groups.city', 'groups.state',
            'groups.postal_code', 'groups.phone', 'groups.display_name',
            'groups.status', 'groups.created_at', 'groups.updated_at',
            'groups.deleted_at', ));
    }

    public static function findByMemberId($memberId)
    {
        $groupMember = GroupMember::where('id', '=', $memberId)->first();

        if (!$groupMember) {
            return;
        }

        return static::where('id', '=', $groupMember->group_id)->first();
    }

    public static function isValidUserForGroup($user_id, $group_id)
    {
        $group = static::where('id', '=', $group_id)->first();

        if (!$group) {
            throw new Exception("Invalid Group ID $group_id");

            return false;
        }

        $member = $group->findMemberByUserId($user_id);

        if (!$member) {
            throw new Exception("Invalid Member ID");

            return false;
        }

        return true;
    }

    public function findUsersByRole($role)
    {
        if (!static::isValidRole($role)) {
            return;
        }

        $members = GroupMember::where('role', '=', $role)
            ->where('group_id', '=', $this->id)->get();

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
            throw new \Exception("You must have a group ID set in order to search for members");
        }

        return GroupMember::where('user_id', '=', $userId)->where('group_id', '=', $this->id)->first();
    }

    public function isGroupOwner($userId)
    {
        $groupMember = $this->findMemberByUserId($userId);

        return $groupMember->role == static::ROLE_OWNER;
    }

    public function getMemberRole($userId)
    {
        $groupMember = $this->findMemberByUserId($userId);

        return $groupMember->role;
    }

    public function addMember($userId, $role = null)
    {
        $groupMember = $this->findMemberByUserId($userId);

        if (!$groupMember) {
            if (is_null($role)) {
                throw new \Exception("You must provide a role if adding a new member");
            }

            if (!isset($this->id) || empty($this->id)) {
                throw new \Exception("The group must have a ID set in order to add a member");
            }

            $groupMember = new GroupMember();
            $groupMember->user_id = $userId;
            $groupMember->role = $role;
            $groupMember->group_id = $this->id;
        } else {
            if (!is_null($role)) {
                $groupMember->role = $role;
            }
        }

        $groupMember->save();
    }

    public static function createIndividualGroup($userId, $input_attrs = [])
    {
        $user = User::find($userId);

        $attrs = array_merge([
            'name' => $user->fname . ' ' . $user->lname,
            'display_name' => $user->fname . ' ' . $user->lname,
            'user_id' => $userId,
            'address1' => $user->address1 || ' ',
            'address2' => $user->address2 || ' ',
            'city' => $user->city || ' ',
            'state' => $user->state || ' ',
            'postal_code' => $user->postal_code || ' ',
            'phone' => $user->phone || ' ',
            'individual' => true,
            'status' => 'pending'
        ], $input_attrs);

        $group = new Group($attrs);
        $group->save();
        $group->addMember($userId, Group::ROLE_OWNER);

        return $group;
    }
}
