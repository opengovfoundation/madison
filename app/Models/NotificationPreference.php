<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Role;
use App\Models\User;

class NotificationPreference extends Model
{
    const TYPE_EMAIL = "email";
    const TYPE_TEXT = "text";

    protected $table = 'notification_preferences';
    protected $fillable = ['event', 'type', 'user_id', 'sponsor_id'];
    public $timestamps = false;

    public function sponsor()
    {
        return $this->belongsTo('App\Models\Sponsor', 'sponsor_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     *  Return notifications registered for a given event.
     *
     * @param string $event
     */
    public static function getActiveNotifications($event)
    {
        return static::where('event', '=', $event)->get();
    }

    /**
     *  Return array of valid notifications for the given user.
     *
     * @return array
     */
    public static function getValidNotificationsForUser(User $user)
    {
        $validNotifications = static::getUserNotifications();

        if ($user->hasRole(Role::ROLE_ADMIN)) {
            $validNotifications = $validNotifications + static::getAdminNotifications();
        }

        return $validNotifications;
    }

    /**
     *  Return array of valid admin notifications.
     *
     *  @return array
     */
    public static function getAdminNotifications()
    {
        $validNotifications = [
            'SponsorNeedsApproval',
        ];

        return static::buildNotificationsFromEventNames($validNotifications);
    }

    /**
     *  Return array of valid user notifications.
     *
     *  @return array
     */
    public static function getUserNotifications()
    {
        $validNotifications = [
        ];

        return static::buildNotificationsFromEventNames($validNotifications);
    }

    protected static function buildNotificationsFromEventNames($names)
    {
        $ret = [];
        foreach ($names as $name) {
            $class = '\App\Notifications\\'.$name;
            $ret[$class::getName()] = $class;
        }

        return $ret;
    }

    public static function addNotificationForUser($event, $user_id, $type = self::TYPE_EMAIL)
    {
        $notification = static::where('user_id', '=', $user_id)
                              ->where('event', '=', $event)
                              ->where('type', '=', $type)
                              ->first();

        if ($notification) {
            return;
        }

        $notification = new static();
        $notification->event = $event;
        $notification->user_id = $user_id;
        $notification->type = $type;

        return $notification->save();
    }

    public static function addNotificationForSponsor($event, $sponsor_id, $type = self::TYPE_EMAIL)
    {
        $notification = static::where('sponsor_id', '=', $sponsor_id)
                              ->where('event', '=', $event)
                              ->where('type', '=', $type)
                              ->first();

        if ($notification) {
            return;
        }

        $notification = new static();
        $notification->event = $event;
        $notification->sponsor_id = $sponsor_id;
        $notification->type = $type;

        return $notification->save();
    }

    public static function addNotificationForAdmin($event, $type = self::TYPE_EMAIL)
    {
        $notification = static::where('user_id', '=', null)
                              ->where('sponsor_id', '=', null)
                              ->where('event', '=', $event)
                              ->where('type', '=', $type)
                              ->first();

        if ($notification) {
            return;
        }

        $notification = new static();
        $notification->event = $event;
        $notification->sponsor_id = null;
        $notification->user_id = null;
        $notification->type = $type;

        return $notification->save();
    }

    public static function removeNotificationForAdmin($event, $type = self::TYPE_EMAIL)
    {
        $notification = static::where('user_id', '=', null)
                              ->where('sponsor_id', '=', null)
                              ->where('event', '=', $event)
                              ->where('type', '=', $type)
                              ->first();

        if ($notification) {
            $notification->delete();
        }
    }

    public static function removeNotificationForUser($event, $user_id, $type = self::TYPE_EMAIL)
    {
        $notification = static::where('user_id', '=', $user_id)
                              ->where('event', '=', $event)
                              ->where('type', '=', $type)
                              ->first();

        if ($notification) {
            $notification->delete();
        }
    }

    public static function removeNotificationForSponsor($event, $sponsor_id, $type = self::TYPE_EMAIL)
    {
        $notification = static::where('sponsor_id', '=', $sponsor_id)
                              ->where('event', '=', $event)
                              ->where('type', '=', $type)
                              ->first();

        if ($notification) {
            $notification->delete();
        }
    }
}
