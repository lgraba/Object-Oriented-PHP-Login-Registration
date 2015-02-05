<?php

require_once 'core/init.php';

$user = new User();
$user->logout();
Session::flash('home', 'You are now logged out!');
Redirect::to('index.php');

?>