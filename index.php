<?php

/*
 * PHP Login Example by Logan Graba
 * February 5, 2015
 * Features:
 * 	SQL Injection Protection
 * 	Cross Site Request Forgery Protection
 *		Salted Password Hash
 *		Fully Object Oriented
 *		Generalized Query Method for Easy Modification
 */


require_once 'core/init.php';
include 'includes/header.php';

?>

		<div id="container">
		
<?php

if(Session::exists('home')) {
	echo '			<p>' . Session::flash('home') . '</p>';
}

$user = new User(); // Current User
if($user->isLoggedIn()) {
	
?>

			<p>Hello, <a href="profile.php?user=<?php echo escape($user->data()->username); ?>"><?php echo escape($user->data()->name); ?></a>!</p>
			
			<ul>
				<li><a href="logout.php">Log Out</a></li>
				<li><a href="update.php">Update Profile</a></li>
				<li><a href="changepassword.php">Change Password</a></li>
			</ul>
	
<?php

	if($user->hasPermission('admin')) {
		echo '			<p>You are an administrator.</p>';
	}
	if($user->hasPermission('moderator')) {
		echo '			<p>You are a moderator.</p>';
	}


} else {
	echo '			<p>You need to <a href="login.php">log in</a> or <a href="register.php">register</a>';
}

?>

		</div>

<?php

include 'includes/footer.php';

?>