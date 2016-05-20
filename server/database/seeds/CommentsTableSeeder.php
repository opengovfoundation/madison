<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class CommentsTableSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = Config::get('madison.seeder.admin_email');
        $adminPassword = Config::get('madison.seeder.admin_password');

        // Login as admin to create docs
        $credentials = array('email' => $adminEmail, 'password' => $adminPassword);
        Auth::attempt($credentials);
        $admin = Auth::user();

        $comment1 = [
            'comment' => [
                'user' => [ 'id' => 1 ],
                'doc' => [ 'id' => 1 ],
                'text' => 'This is a comment'
            ]
        ];

        Input::replace($comment1);
        App::make('App\Http\Controllers\CommentController')->postIndex($comment1['comment']['doc']['id']);

        // This comment is a reply to the first comment
        // Comment model gets user ID making comment from the payload, so this
        // will belong to 'user@example.com'
        $comment1_reply = [
            'comment' => [
                'user' => [ 'id' => 1 ],
                'doc' => [ 'id' => 1 ],
                'text' => 'Comment reply',
                'parent_id' => 1
            ]
        ];

        Input::replace($comment1_reply);
        App::make('App\Http\Controllers\CommentController')->postComments($comment1_reply['comment']['doc']['id'], $comment1_reply['comment']['parent_id']);

        $comment2 = [
            'comment' => [
                'user' => [ 'id' => 1 ],
                'doc' => [ 'id' => 1 ],
                'text' => 'Yet another comment'
            ]
        ];

        Input::replace($comment2);
        App::make('App\Http\Controllers\CommentController')->postIndex($comment2['comment']['doc']['id']);
    }
}
