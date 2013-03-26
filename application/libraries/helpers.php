<?php
class Helpers{
	
	//Generates Facebook login url
	public static function fbLogin($redirect = ''){
		$facebook = IoC::resolve('facebook-sdk');
		
		//Redirect to the home page if no url was given
		$redirect = $redirect == '' ? URL::home() : $redirect;
		
		$params = array(
			'redirect_uri' => $redirect,
			'display' => 'popup',
			'scope' => 'email' 
		);
		
		return $facebook->getLoginUrl($params);
	}
}
