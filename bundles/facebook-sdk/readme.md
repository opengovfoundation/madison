# Facebook SDK

## Installation

### Aritsan

	php artisan bundle:install facebook-sdk

### Bundle Registration

Add the following to your **application/bundles.php** file:

	'facebook-sdk' => array('auto' => true),

## Configuration

Add the following to your **application/config/facebook.php** file:

	return array(
		'app_id' => '',
		'secret' => '',
	);
	
## Usage

	$facebook = IoC::resolve('facebook-sdk');
	$uid = $facebook->getUser();
	
Fork of https://github.com/facebook/facebook-php-sdk