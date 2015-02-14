<?php

use Illuminate\Database\Seeder;

class DocumentsTableSeeder extends Seeder
{
	public function run()
	{
		if(file_exists(app_path() . '/config/creds.yml')) {
			$creds = yaml_parse_file(app_path() . '/config/creds.yml');
		}

    // Login as admin to create docs
		$credentials = array('email' => $creds['admin_email'], 'password' => $creds['admin_password']);
    Auth::attempt($credentials);
    $admin = Auth::user();
    $mx_a = Group::where('id', '=', 1)->first();

    // Create first doc

    $docSeedPath = app_path() . '/database/seeds/the_last_question.md';
    if(file_exists($docSeedPath)) {
      $content = file_get_contents($docSeedPath);
    }
    $docOptions = array(
      'title'       => 'The Last Question',
      'content'     => $content,
      'sponsor'     => $mx_a->id,
      'sponsorType' => Doc::SPONSOR_TYPE_GROUP
    );
    $document = Doc::createEmptyDocument($docOptions);

    Input::replace($input = ['content' => $content]);
    App::make('DocumentsController')->saveDocumentEdits($document->id);

    // Create second doc

    $docSeedPath = app_path() . '/database/seeds/log_reg.md';
    if(file_exists($docSeedPath)) {
      $content = file_get_contents($docSeedPath);
    }

    $docOptions = array(
      'title'       => 'Logistic Regression',
      'sponsor'     => $mx_a->id,
      'sponsorType' => Doc::SPONSOR_TYPE_GROUP
    );
    $document = Doc::createEmptyDocument($docOptions);

		DB::table('doc_contents')->insert(array(
      'doc_id'      => $document->id,
      'content'     => $content,
		));
	}
}
