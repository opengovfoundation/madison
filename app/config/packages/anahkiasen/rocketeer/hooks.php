<?php return array(

	// Tasks
	//
	// Here you can define in the `before` and `after` array, Tasks to execute
	// before or after the core Rocketeer Tasks. You can either put a simple command,
	// a closure which receives a $task object, or the name of a class extending
	// the Rocketeer\Traits\Task class
	//
	// In the `custom` array you can list custom Tasks classes to be added
	// to Rocketeer. Those will then be available in Artisan
	// as `php artisan deploy:yourtask`
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
				$task->runForCurrentRelease('php artisan migrate');

				$homeFolder = $task->rocketeer->getHomeFolder();
				$cred_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder . '/shared/creds.yml current/app/config/creds.yml');
				$smtp_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder . '/shared/smtp.yml current/app/config/smtp.yml');
				$uservoice_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder .'/shared/uservoice.js current/public/js/uservoice.js');
				$addthis_ret = $task->runInFolder('/', 'ln -s ' . $homeFolder . '/shared/addthis.js current/public/js/addthis.js');
			}
		),
		'cleanup' => array(),
	),

	// Custom Tasks to register with Rocketeer
	'custom' => array(),

);
