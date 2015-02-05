<?php

require_once 'core/init.php';
include 'includes/header.php';

if(!$username = Input::get('user')) {
	Redirect::to('index.php');
} else {
	$user = new User($username);
	if(!$user->exists()) {
		Redirect::to(404);
	} else {
		$data = $user->data();
	}
?>

		<div id="container">
			<h3>Profile for <?php echo escape($data->username); ?></h3>
			<p>Full Name: <?php echo escape($data->name); ?></p>
		</div>

<?php
}

include 'includes/footer.php';

?>