<?php 
$I = new FunctionalTester($scenario);
$I->am('a developer');
$I->wantTo('Backup the database');

$I->runShellCommand('php artisan db:backup');
$I->seeInShellOutput('Backup created.');

$I->wantTo('Check that at most 10 backups exist');

$path = storage_path() . '/db_backups';
$files = scandir($path);
$files = array_diff($files, array('.', '..', '.gitignore'));

$I->assertGreaterThenOrEqual(count($files), 10, "Counting number of files");