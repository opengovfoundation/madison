<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Annotation;

class AnnotationsTableSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = Config::get('madison.seeder.admin_email');
        $adminPassword = Config::get('madison.seeder.admin_password');

        // Login as admin to create docs
        $credentials = array('email' => $adminEmail, 'password' => $adminPassword);
        Auth::attempt($credentials);
        $admin = Auth::user();

        $annotation1 = [
            'user_id' => 1,
            'doc_id' => 1,
            'quote' => 'Document',
            'text' => 'Annotation!',
            'uri' => '/docs/example-document',
            'tags' => [],
            'comments' => [],
            'ranges' => [
                [
                    'start' => '/p[1]',
                    'end' => '/p[1]',
                    'startOffset' => 4,
                    'endOffset' => 12
                ]
            ]
        ];

        Input::replace($annotation1);
        App::make('App\Http\Controllers\AnnotationApiController')->postIndex($annotation1['doc_id']);

        $annotation2 = [
            'user_id' => 1,
            'doc_id' => 1,
            'quote' => 'Content',
            'text' => 'Another Annotation!',
            'uri' => '/docs/example-document',
            'tags' => [],
            'comments' => [],
            'ranges' => [
                [
                    'start' => '/p[1]',
                    'end' => '/p[1]',
                    'startOffset' => 13,
                    'endOffset' => 20
                ]
            ]
        ];

        Input::replace($annotation2);
        App::make('App\Http\Controllers\AnnotationApiController')->postIndex($annotation2['doc_id']);
    }
}
