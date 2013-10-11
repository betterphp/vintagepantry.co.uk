<?php

if (isset($_POST['email'], $_POST['password'])){
	$user = user::fetch_by_email($_POST['email']);
	
	if ($user === false){
		$errors[] = 'No user found with that email address.';
	}else if (!$user->is_correct_password($_POST['password'])){
		$errors[] = 'Incorrect password.';
	}
	
	if (empty($errors)){
		$_SESSION['user'] = $user;
		redirect('admin.html');
	}
}

if (!empty($errors)){
	foreach ($errors as $error){
		echo '<div class="msg error">', $error, '</div>';
	}
}

?>
<form action="" method="post">
	<div>
		<label for="email">Email</label>
		<input type="text" class="text" name="email" id="email" />
	</div>
	<div>
		<label for="password">Password</label>
		<input type="password" class="text" name="password" id="password" />
	</div>
	<div>
		<input type="submit" class="button" value="Login" />
	</div>
</form>