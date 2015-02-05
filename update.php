<?php

require_once 'core/init.php';
include 'includes/header.php';

?>

		<div id="container">
		
<?php

$user = new User();

if(!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {
		
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'name' => array(
				'required' => true,
				'min' => 2,
				'max' => 50
			)
		));
		
		if($validation->passed()) {
			
			try {
				$user->update(array(
					'name' => Input::get('name')
				));
				
				Session::flash('home', 'Your profile has been updated.');
				Redirect::to('index.php');
				
			} catch(Exception $e) {
				die($e->getMessage());
			}
			
		} else {
			foreach($validation->errors() as $error) {
				echo '<div class="error">', $error, '</div>';
			}
		}
		
	}
}

?>

			<form action="" method="post">
				<div class="field">
					<label for="name">Name</label>
					<input type="text" name="name" value="<?php echo escape($user->data()->name); ?>" />
				</div>
				
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
				<input class="button" type="submit" value="Update" />
			</form>
			
		</div>

<?php

include 'includes/footer.php';

?> 