<?php return array(

	// Tasks
	//
	// Here you can define in the `before` and `after` array, Tasks to execute
	// before or after the core Rocketeer Tasks. You can either put a simple command,
	// a closure which receives a $task object, or the name of a class extending
	// the Rocketeer\Traits\Task class
	//
	// In the `custom` array you can list custom Tasks classes to be added
	// to Rocketeer. Those will then be available in the command line
	// with all the other tasks
	//////////////////////////////////////////////////////////////////////

	// Tasks to execute before the core Rocketeer Tasks
	'before' => array(
		'setup'   => array(),
		'deploy'  => array(),
		'cleanup' => array(),
	),

	// Tasks to execute after the core Rocketeer Tasks
	'after' => array(
		'setup'   => array(),
		'deploy'  => array(
			function($task){
				$homeFolder = $task->rocketeer->getFolder('shared');

				$task->command->info('Linking ' . $homeFolder . '/creds.yml -> current/app/config/creds.yml');
				$cred_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder . '/creds.yml current/app/config/creds.yml');

				$task->command->info('Linking ' . $homeFolder . '/oauth_creds.yml -> current/app/config/oauth_creds.yml');
				$cred_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder . '/oauth_creds.yml current/app/config/oauth_creds.yml');
				
				$task->command->info('Linking ' . $homeFolder . '/smtp.yml -> current/app/config/creds.yml');
				$smtp_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder . '/smtp.yml current/app/config/smtp.yml');
				
				$task->command->info('Linking ' . $homeFolder . '/uservoice.js -> current/public/js/uservoice.js');
				$uservoice_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder .'/uservoice.js current/public/js/uservoice.js');
				
				$task->command->info('Linking ' . $homeFolder . '/addthis.js -> current/public/js/addthis.js');
				$addthis_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder . '/addthis.js current/public/js/addthis.js');

				$task->command->info('Linking ' . $homeFolder . '/ga.js -> current/public/js/ga.js');
				$ga_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder . '/ga.js current/public/js/ga.js');

				$task->command->info('Running Migrations');
				$task->runForCurrentRelease('php artisan migrate');
			}
		),
		'cleanup' => array(),
	),

	// Custom Tasks to register with Rocketeer
	'custom' => array(
		'MadisonTasks\Migrate'
	),

);
