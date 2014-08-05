<?php
/*
|--------------------------------------------------------------------------
| oAuth Config
|--------------------------------------------------------------------------
*/

if(file_exists(app_path() . '/config/oauth_creds.yml')){
	$consumers = yaml_parse_file(app_path() . '/config/oauth_creds.yml');
}

return array(
	'storage' => 'Session',
	'consumers' => $consumers
);
