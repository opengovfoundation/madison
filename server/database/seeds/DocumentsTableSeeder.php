<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use App\Models\Doc;
use App\Models\Setting;

class DocumentsTableSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = Config::get('madison.seeder.admin_email');
        $adminPassword = Config::get('madison.seeder.admin_password');

        // Login as admin to create docs
        $credentials = array('email' => $adminEmail, 'password' => $adminPassword);
        Auth::attempt($credentials);
        $admin = Auth::user();
        $group = Group::where('id', '=', 1)->first();

        // Create first doc

        $docSeedPath = app_path().'/../database/seeds/docs/example.md';
        if (file_exists($docSeedPath)) {
            $content = file_get_contents($docSeedPath);
        } else {
            $content = "New Document Content";
        }
        $docOptions = array(
            'title'         => 'Example Document',
            'content'       => $content,
            'sponsor'       => $group->id,
            'publish_state' => 'published'
        );
        $document = Doc::createEmptyDocument($docOptions);

        //Set first doc as featured doc
        $featuredSetting = new Setting();
        $featuredSetting->meta_key = 'featured-doc';
        $featuredSetting->meta_value = $document->id;
        $featuredSetting->save();

        // Create second doc

        $docSeedPath = app_path().'/../database/seeds/docs/example2.md';
        if (file_exists($docSeedPath)) {
            $content = file_get_contents($docSeedPath);
        } else {
            $content = "New Document Content";
        }

        $docOptions = array(
            'title'         => 'Second Example Document',
            'sponsor'       => $group->id,
            'publish_state' => 'published'
        );
        $document = Doc::createEmptyDocument($docOptions);
    }
}
