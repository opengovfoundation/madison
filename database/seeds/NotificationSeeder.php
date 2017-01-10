<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\NotificationPreference;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Enable all notifications for all users
        $users = User::all();

        foreach ($users as $user) {
            $events = NotificationPreference::getValidNotificationsForUser($user);

            foreach ($events as $eventName => $eventClass) {
                NotificationPreference::addNotificationForUser($eventName, $user->id);
            }
        }

    }
}
