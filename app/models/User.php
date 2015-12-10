<?php
/**
 * 	User Model.
 */
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class User extends Eloquent implements UserInterface, RemindableInterface
{
    use Zizaco\Entrust\HasRole;

    protected $hidden = array('password', 'token', 'last_login', 'deleted_at', 'oauth_vendor', 'oauth_id', 'oauth_update', 'roles');
    protected $appends = array('display_name');
    protected $softDelete = true;

    /**
     *	Validation rules.
     */
    protected static $rules = array(
      'save' => array(
      'fname'    => 'required',
      'lname'    => 'required',
        ),
      'create' => array(
        'email'            => 'required|unique:users',
        'password'    => 'required',
      ),
      'social-signup'    => array(
        'email'            => 'required|unique:users',
        'oauth_vendor'    => 'required',
        'oauth_id'            => 'required',
        'oauth_update'    => 'required',
        ),
        'twitter-signup'    => array(
      'oauth_vendor'    => 'required',
      'oauth_id'            => 'required',
      'oauth_update'    => 'required',
    ),
    'update'    => array(
      'email'            => 'required|unique:users',
      'password'    => 'required',
        ),
        'verify'    => array(
      'phone'            => 'required',
        ),
    );

    /**
     *	Custom error messages for certain validation requirements.
     */
    protected static $customMessages = array(
        'fname.required' => 'The first name field is required.',
        'lname.required' => 'The last name field is required.',
        'phone.required' => 'A phone number is required to request verified status.',
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
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = array())
    {
        if (!$this->beforeSave()) {
            return false;
        }

        //Don't want user model trying to save validationErrors field.
        //	TODO: I'm sure Eloquent can handle this.  What's the setting for ignoring fields when saving?
        unset($this->validationErrors);
        unset($this->rules);
        unset($this->verify);

        return parent::save($options);
    }

    /**
     *	getErrors.
     *
     *	Returns errors from validation
     *
     *	@param void
     *
     * @return MessageBag $this->validationErrors
     */
    public function getErrors()
    {
        return $this->validationErrors;
    }

    /**
     *	verified.
     *
     *	Returns the value of the UserMeta for this user with key 'verify'
     *		The value of this is either 'verified' or 'pending'
     *		If the user hasn't requested verified status, this will return null
     *
     *	@param void
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
     *	getDisplayName.
     *
     *	Returns the user's display name
     *
     *	@param void
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

    /**
     *	docs.
     *
     *	Eloquent one-to-many relationship for Doc
     *
     * @param void
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function docs()
    {
        return $this->belongsToMany('Doc');
    }

    /**
     *	activeGroup.
     *
     *	Returns current active group for this user
     *		Grabs the active group id from Session
     *
     *	@param void
     *
     *	@return Group|| new Group
     *
     *	@todo Why would this return a new group?  Should probalby return some falsy value.
     */
    public function activeGroup()
    {
        $activeGroupId = Session::get('activeGroupId');

        if ($activeGroupId <= 0) {
            //return new Group();
            return;
        }

        return Group::where('id', '=', $activeGroupId)->first();
    }

    /**
     *	setPasswordAttribute.
     *
     *	Mutator method for the password attribute
     *		Hashes the password and sets the attribute
     *
     *	@param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     *	groups.
     *
     *	Eloquent belongsToMany relationship for Group
     *
     *	@param void
     *
     *	@return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('Group', 'group_members');
    }

    /**
     *	comments.
     *
     *	Eloquent hasMany relationship for Comment
     *
     *	@param void
     *
     *	@return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('Comment');
    }

    /**
     *	annotations.
     *
     *	Eloquent hasMany relationship for Annoation
     *
     *	@param void
     *
     *	@return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function annotations()
    {
        return $this->hasMany('Annotation');
    }

    /**
     *	getAuthIdentifier.
     *
     *	Determines value used by Laravel's Auth class to identify users
     *		Uses the user id
     *
     *	@param void
     *
     *	@return int $this->id
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     *	getAuthPassword.
     *
     *	Determines value used by Laravel's Auth class to authenticate users
     *		Uses the user password
     *
     *	@param void
     *
     *	@return string $this->password
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     *	getReminderEmail.
     *
     *	Determines value to use for reminder emails
     *		Uses the user email
     *
     *	@param void
     *
     *	@return string $this->email
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     *	organization.
     *
     *	Eloquent belongsTo relationship for Organization
     *
     *	@param void
     *
     *	@return Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     *	@todo This can be removed as we use Groups in place of Organizations
     */
    public function organization()
    {
        return $this->belongsTo('Organization');
    }

    /**
     *	note_meta.
     *
     *	Eloquent hasMany relationship for NoteMeta
     *
     *	@param void
     *
     *	@return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function note_meta()
    {
        return $this->hasMany('NoteMeta');
    }

    /**
     *	user_meta.
     *
     *	Eloquent hasMany relationship for UserMeta
     *
     *	@param void
     *
     *	@return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_meta()
    {
        return $this->hasMany('UserMeta');
    }

    /**
     *	getSponsorStatus.
     *
     *	Returns the value of the UserMeta for this user with key 'independent_sponsor'
     *		The value of this is either '1' or '0'
     *		If the user hasn't requested independent sponsor status, this will return null
     *
     *	@param void
     *
     * @return string||null
     */
    public function getSponsorStatus()
    {
        $result = $this->user_meta()->where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_SPONSOR)->first();
        if ($result) {
            return (bool) $result->meta_value;
        } else {
            return;
        }
    }

    /**
     *	setIndependentAuthor.
     *
     *	Sets the Independent Sponsor status for this user
     *		Sets / Creates a UserMeta for this user with key = 'independent_sponsor'
     *		and value '1'||'0' based on input boolean
     *
     *	@param bool $bool
     */
    public function setIndependentAuthor($bool)
    {
        if ($bool) {
            DB::transaction(function () {
                $metaKey = UserMeta::where('user_id', '=', $this->id)
                                   ->where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_SPONSOR)
                                   ->first();

                if (!$metaKey) {
                    $metakey = new UserMeta();
                    $metaKey->user_id = $this->id;
                    $metaKey->meta_key = 'independent_author';
                }

                $metaKey->meta_value = $bool ? 1 : 0;
                $metaKey->save();
            });
        }
    }

    /**
     *	admin_contact.
     *
     *	Sets the user as an admin contact for the site
     *
     *	@param unknownType $setting
     *
     *	@return bool||void
     *
     *	@todo References to this should be removed.  We're allowing all admins to determine notification subscriptions
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
     *	doc_meta.
     *
     *	Eloquent hasMany relationship for DocMeta
     *
     *	@param void
     *
     *	@return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function doc_meta()
    {
        return $this->hasMany('DocMeta');
    }

    /**
     *	getValidSponsors.
     *
     *	@todo I'm not sure what exactly this does at first glance
     */
    public function getValidSponsors()
    {
        $collection = new Collection();

        $groups = GroupMember::where('user_id', '=', $this->id)
                             ->whereIn('role', array(Group::ROLE_EDITOR, Group::ROLE_OWNER))
                             ->get();

        foreach ($groups as $groupMember) {
            $collection->add($groupMember->group()->first());
        }

        $users = UserMeta::where('user_id', '=', $this->id)
                          ->where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_SPONSOR)
                          ->where('meta_value', '=', '1')
                          ->get();

        foreach ($users as $userMeta) {
            $collection->add($userMeta->user()->first());
        }

        return $collection;
    }

    /**
     *	findByRoleName.
     *
     *	Returns all users with a given role
     *
     *	@param string $role
     *
     *	@return Illuminate\Database\Eloquent\Collection
     */
    public static function findByRoleName($role)
    {
        return Role::where('name', '=', $role)->first()->users()->get();
    }

    /**
     *	beforeSave.
     *
     *	Validates before saving.  Returns whether the User can be saved.
     *
     *	@param array $options
     *
     * @return bool
     */
    private function beforeSave(array $options = array())
    {
        $this->rules = $this->mergeRules();

        if (!$this->validate()) {
            Log::error("Unable to validate user: ");
            Log::error($this->getErrors()->toArray());
            Log::error($this->attributes);

            return false;
        }

        return true;
    }

    /**
     *	mergeRules.
     *
     *	Merge the rules arrays to form one set of rules
     *
     * @param void
     *
     * @return array $output
     *
     * @todo handle social login / signup rule merges
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
        //If we're signing up via Oauth
        elseif (isset($this->oauth_vendor)) {
            switch ($this->oauth_vendor) {
                case 'twitter':
                    $merged = array_merge_recursive($rules['save'], $rules['twitter-signup']);
                    break;
                case 'facebook':
                case 'linkedin':
                    $merged = array_merge_recursive($rules['save'], $rules['social-signup']);
                    break;
                default:
                    throw new Exception("Unknown OAuth vendor: ".$this->oauth_vendor);
            }
        }
        //If we're creating a user via Madison
        else {
            $merged = array_merge_recursive($rules['save'], $rules['create']);
        }

        //Include verify rules if requesting verification
        if (isset($this->verify)) {
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
     *	Validate.
     *
     *	Validate input against merged rules
     *
     *	@param array $attributes
     *
     * @return bool
     */
    public function validate()
    {
        $validation = Validator::make($this->attributes, $this->rules, static::$customMessages);

        if ($validation->passes()) {
            return true;
        }

        $this->validationErrors = $validation->messages();

        return false;
    }
}
