<?php

namespace App\Models;

/**
 * User Model.
 */
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Session;
use Hash;
use Log;

use App\Models\Annotation;
use App\Models\Sponsor;
use App\Models\SponsorMember;
use App\Models\Role;
use App\Models\UserMeta;

use DB;

use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;
    use SoftDeletes { SoftDeletes::restore insteadof EntrustUserTrait; }

    protected $dates = ['deleted_at'];

    protected $hidden = ['password', 'token', 'last_login', 'deleted_at', 'roles', 'remember_token'];
    protected $fillable = ['fname', 'lname', 'email', 'password', 'token'];
    protected $appends = ['display_name', 'independent_sponsor'];

    const STATUS_VERIFIED = 'verified';
    const STATUS_PENDING = 'pending';
    const STATUS_DENIED = 'denied';

    /**
     * Validation rules.
     */
    protected static $rules = [
        'save' => [
            'fname' => 'required',
            'lname' => 'required',
        ],
        'create' => [
            'email' => 'required|unique:users',
            'password' => 'required',
        ],
        'update' => [
            'email' => 'required|unique:users',
            'password' => 'required',
        ],
        'verify' => [
            'phone' => 'required',
        ],
    ];

    protected $validationErrors = null;
    protected $verify = false;

    /**
     *  Custom error messages for certain validation requirements.
     */
    protected static $customMessages = array(
        'fname.required' => 'The first name field is required.',
        'lname.required' => 'The last name field is required.',
        'phone.required' => 'A phone number is required to request verified status.',
    );

    /**
     *  Constructor.
     *
     *  @param array $attributes
     *  Extends Eloquent constructor
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->validationErrors = new MessageBag();
    }

    /**
     *  Save.
     *
     *  Override Eloquent save() method
     *      Runs $this->beforeSave()
     *
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = array())
    {
        if (!$this->beforeSave()) {
            return false;
        }

        return parent::save($options);
    }

    /**
     *  getErrors.
     *
     *  Returns errors from validation
     *
     *  @param void
     *
     * @return MessageBag $this->validationErrors
     */
    public function getErrors()
    {
        return $this->validationErrors;
    }

    /**
     *  verified.
     *
     *  Returns the value of the UserMeta for this user with key 'verify'
     *      The value of this is either 'verified' or 'pending'
     *      If the user hasn't requested verified status, this will return null
     *
     *  @param void
     *
     * @return string||null
     */
    public function verified()
    {
        $request = $this->user_meta()->where('meta_key', 'verify')->first();

        if (isset($request)) {
            return $request->meta_value;
        } else {
            return;
        }
    }

    /**
     *  getDisplayName.
     *
     *  Returns the user's display name
     *
     *  @param void
     *
     * @return string
     */
    public function getDisplayName()
    {
        return "{$this->fname} {$this->lname}";
    }

    /**
     *  getDisplayNameAttribute()
     *
     *  Alias for getDisplayName() used during serialization.
     *
     *  @param void
     *  @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getNameAttribute()
    {
        return $this->getDisplayName();
    }

    /**
     * getIndependentSponsorAttribute
     *
     * Returns the user's independent sponsor status.
     *
     * @param void
     * @return string
     */
    public function getIndependentSponsorAttribute()
    {
        // check if user has sponsor marked as individual
        // return the status of that sponsor
        $individual_sponsor = $this->sponsors->where('individual', 1)->first();
        return $individual_sponsor ? $individual_sponsor->status : null;
    }

    /**
     *  activeSponsor.
     *
     *  Returns current active sponsor for this user
     *      Grabs the active sponsor id from Session
     *
     *  @param void
     *
     *  @return null || Sponsor
     */
    public function activeSponsor()
    {
        $activeSponsorId = Session::get('activeSponsorId');

        if ($activeSponsorId <= 0) {
            return;
        }

        return Sponsor::where('id', '=', $activeSponsorId)->first();
    }

    /**
     *  setPasswordAttribute.
     *
     *  Mutator method for the password attribute
     *      Hashes the password and sets the attribute
     *
     *  @param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     *  sponsors.
     *
     *  Eloquent belongsToMany relationship for Sponsor
     *
     *  @param void
     *
     *  @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sponsors()
    {
        return $this->belongsToMany('App\Models\Sponsor', 'sponsor_members')->whereNull('sponsor_members.deleted_at');
    }

    /**
     * individualSponsor
     *
     * Eloquent belongsTo relationship for an independent sponsor sponsor
     *
     * @param void
     * @return App\Models\Sponsor
     */
    public function individualSponsor()
    {
        return $this->sponsors->where('individual', true)->first();
    }

    public function comments()
    {
        return $this->annotations()->where('annotation_type_type', Annotation::TYPE_COMMENT);
    }

    public function getCommentsAttribute()
    {
        return $this->comments()->get();
    }

    /**
     *  annotations.
     *
     *  Eloquent hasMany relationship for Annotation
     *
     *  @param void
     *
     *  @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function annotations()
    {
        return $this->hasMany('App\Models\Annotation');
    }

    public function notificationPreferences()
    {
        return $this->hasMany('App\Models\NotificationPreference');
    }

    /**
     *  getAuthIdentifier.
     *
     *  Determines value used by Laravel's Auth class to identify users
     *      Uses the user id
     *
     *  @param void
     *
     *  @return int $this->id
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     *  getAuthPassword.
     *
     *  Determines value used by Laravel's Auth class to authenticate users
     *      Uses the user password
     *
     *  @param void
     *
     *  @return string $this->password
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     *  getReminderEmail.
     *
     *  Determines value to use for reminder emails
     *      Uses the user email
     *
     *  @param void
     *
     *  @return string $this->email
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     *  user_meta.
     *
     *  Eloquent hasMany relationship for UserMeta
     *
     *  @param void
     *
     *  @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_meta()
    {
        return $this->hasMany('App\Models\UserMeta');
    }

    /**
     *  admin_contact.
     *
     *  Sets the user as an admin contact for the site
     *
     *  @param unknownType $setting
     *
     *  @return bool||void
     *
     *  @todo References to this should be removed.  We're allowing all admins to determine notification subscriptions
     */
    public function admin_contact($setting = null)
    {
        if (isset($setting)) {
            $meta = $this->user_meta()->where('meta_key', '=', 'admin_contact')->first();

            if (!isset($meta)) {
                $meta = new UserMeta();
                $meta->user_id = $this->id;
                $meta->meta_key = 'admin_contact';
                $meta->meta_value = $setting;
                $meta->save();

                return true;
            } else {
                $meta->meta_value = $setting;
                $meta->save();

                return true;
            }
        }

        if (!$this->hasRole('Admin')) {
            return false;
        }

        $meta = $this->user_meta()->where('meta_key', '=', 'admin_contact')->first();

        if (isset($meta)) {
            $this->admin_contact = $meta->meta_value == '1' ? true : false;
        } else {
            $this->admin_contact = false;
        }
    }

    /**
     *  doc_meta.
     *
     *  Eloquent hasMany relationship for DocMeta
     *
     *  @param void
     *
     *  @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function doc_meta()
    {
        return $this->hasMany('App\Models\DocMeta');
    }

    /**
     *  getValidSponsors.
     *
     *  @todo I'm not sure what exactly this does at first glance
     */
    public function getValidSponsors()
    {
        $collection = new Collection();

        $sponsors = SponsorMember::where('user_id', '=', $this->id)
                             ->whereIn('role', array(Sponsor::ROLE_EDITOR, Sponsor::ROLE_OWNER))
                             ->get();

        foreach ($sponsors as $sponsorMember) {
            $collection->add($sponsorMember->sponsor()->first());
        }

        return $collection;
    }

    /**
     *  findByRoleName.
     *
     *  Returns all users with a given role
     *
     *  @param string $role
     *
     *  @return Illuminate\Database\Eloquent\Collection
     */
    public static function findByRoleName($role)
    {
        $role = Role::where('name', '=', $role)->first();
        return $role ? $role->users()->get() : [];
    }

    /**
     *  hasRole.
     *
     *  Returns a boolean if the user has the given role or not.  This overrides
     *  the default hasRole() from Entrust.
     *
     *  This is just a temporary hotfix, using the Entrust Role object anywhere
     *  is causing an uncatchable fatal error in PHP on Laravel Forge.  While we
     *  investigate that, this will suffice in the meantime.
     *
     *  @param string $role
     *
     *  @return bool
     */
    public function hasRole($role)
    {
        $results = DB::select(
            DB::raw('SELECT COUNT(*) AS count ' .
                'FROM role_user LEFT JOIN roles ' .
                'ON role_user.role_id = roles.id ' .
                'WHERE role_user.user_id = :userid ' .
                'AND roles.name = :role'),
            array('userid' => $this->id, 'role' => $role));

        if ($results && isset($results[0]) && isset($results[0]->count) && $results[0]->count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  beforeSave.
     *
     *  Validates before saving.  Returns whether the User can be saved.
     *
     *  @param array $options
     *
     * @return bool
     */
    private function beforeSave(array $options = array())
    {
        if (!$this->validate()) {
            Log::error("Unable to validate user: ");
            Log::error($this->getErrors()->toArray());
            Log::error($this->attributes);

            return false;
        }

        return true;
    }

    /**
     *  mergeRules.
     *
     *  Merge the rules arrays to form one set of rules
     *
     * @param void
     *
     * @return array $output
     */
    public function mergeRules()
    {
        $rules = static::$rules;
        $output = array();

        //If we're updating the user
        if ($this->exists) {
            $merged = array_merge_recursive($rules['save'], $rules['update']);
            $merged['email'] = 'required|unique:users,email,'.$this->id;
        }
        //If we're creating a user via Madison
        else {
            $merged = array_merge_recursive($rules['save'], $rules['create']);
        }

        //Include verify rules if requesting verification
        if ($this->verify) {
            $merged = array_merge_recursive($merged, $rules['verify']);
        }

        foreach ($merged as $field => $rules) {
            if (is_array($rules)) {
                $output[$field] = implode("|", $rules);
            } else {
                $output[$field] = $rules;
            }
        }

        return $output;
    }

    /**
     *  Validate.
     *
     *  Validate input against merged rules
     *
     *  @param array $attributes
     *
     * @return bool
     */
    public function validate()
    {
        // `mergeRules` handles logic for determining the context of the
        // validation, eg: save, update, create, etc
        $validation = Validator::make($this->attributes, $this->mergeRules(), static::$customMessages);

        if ($validation->passes()) {
            return true;
        }

        $this->validationErrors = $validation->messages();

        return false;
    }

    /**
     * Remember Token functions, as specified by Laravel Upgrade guide.
     * http://laravel.com/docs/5.1/upgrade#upgrade-4.1.26
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function isAdmin()
    {
        return $this->hasRole(Role::ROLE_ADMIN);
    }

    public function makeAdmin()
    {
        if ($this->isAdmin()) {
            return $this;
        }

        $adminRole = Role::adminRole();
        $this->attachRole($adminRole);

        return $this;
    }

    public function removeAdmin()
    {
        if (!$this->isAdmin()) {
            return $this;
        }

        $adminRole = Role::adminRole();
        $this->detachRole($adminRole);

        return $this;
    }
}
