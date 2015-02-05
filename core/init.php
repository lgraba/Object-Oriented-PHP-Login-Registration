<?php

session_start();

// Define configuration parameters
$GLOBALS['config'] = array(
	'mysql' => array(
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' => '',
		'db' => 'ooplr'
	),
	'remember' => array(
		'cookie_name' => 'hash',
		'cookie_expiry' => 604800  // One week in seconds
	),
	'session' => array(
		'session_name' => 'user',
		'token_name' => 'token'
	)
);

// Autoload classes
spl_autoload_register(function($class) {
	require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';

// Check for cookie hash and log user in based on user_id returned from search of database table user_session for that hash.
if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
	$hash = Cookie::get(Config::get('remember/cookie_name'));
	$hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));
	
	if($hashCheck->count()) {
		$user = new User($hashCheck->first_result()->user_id);
		$user->login();
	}
	
} 

?>