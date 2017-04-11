<?php

namespace App\Models;

use Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\MessageBag;
use Log;
use Session;

use App\Models\Annotation;
use App\Models\Sponsor;
use App\Models\SponsorMember;
use App\Models\Role;

use DB;

use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Authorizable;
    use Notifiable;
    use EntrustUserTrait {
        Authorizable::can insteadof EntrustUserTrait;
        EntrustUserTrait::can as hasPermission;
    }
    use SoftDeletes { SoftDeletes::restore insteadof EntrustUserTrait; }

    protected $dates = ['deleted_at'];

    protected $hidden = ['password', 'token', 'last_login', 'deleted_at', 'roles', 'remember_token'];
    protected $fillable = ['fname', 'lname', 'email', 'password', 'token'];
    protected $appends = ['display_name'];

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

    public function getAvatarAttribute()
    {
        $gravatarHash = md5(strtolower(trim($this->email ?: $this->display_name)));
        return "https://www.gravatar.com/avatar/$gravatarHash?s=45&d=mm";
    }
}
