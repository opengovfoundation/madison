<?php

use Illuminate\Database\Seeder;

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

    $docSeedPath = app_path().'/database/seeds/example.md';
        if (file_exists($docSeedPath)) {
            $content = file_get_contents($docSeedPath);
        } else {
          $content = "New Document Content";
        }
        $docOptions = array(
      'title'       => 'Example Document',
      'content'     => $content,
      'sponsor'     => $group->id,
      'sponsorType' => Doc::SPONSOR_TYPE_GROUP,
    );
        $document = Doc::createEmptyDocument($docOptions);

        Input::replace($input = ['content' => $content]);
        App::make('DocumentsController')->saveDocumentEdits($document->id);

    // Create second doc

    $docSeedPath = app_path().'/database/seeds/example2.md';
        if (file_exists($docSeedPath)) {
            $content = file_get_contents($docSeedPath);
        } else {
          $content = "New Document Content";
        }

        $docOptions = array(
      'title'       => 'Second Example Document',
      'sponsor'     => $group->id,
      'sponsorType' => Doc::SPONSOR_TYPE_GROUP,
    );
        $document = Doc::createEmptyDocument($docOptions);

        DB::table('doc_contents')->insert(array(
      'doc_id'      => $document->id,
      'content'     => $content,
        ));
    }
}
