<?php

if (isset($_POST['name'], $_POST['email'], $_POST['message'])){
	$errors = array();
	
	if (empty($_POST['name'])){
		$errors[] = 'You must enter your name.';
	}else if (!preg_match('#^[a-z ]+$#i', $_POST['name'])){
		$errors[] = 'The name you entered is not valid.';
	}
	
	if (empty($_POST['email'])){
		$errors[] = 'You must enter your email address.';
	}else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$errors[] = 'The email address you entered is not valid.';
	}
	
	if (empty($_POST['message'])){
		$errors[] = 'You must enter a message.';
	}
	
	if (empty($errors)){
		$query = http_build_query(array('email' => $_POST['email'], 'ip' => $_SERVER['REMOTE_ADDR'], 'f' => 'serial'));
		
		$data = @file_get_contents('http://www.stopforumspam.com/api?' . $query);
		
		if ($data !== false){
			$data = unserialize($data);
			
			if ($data['success']){
				foreach ($data as $check){
					if ($check['appears']){
						$errors[] = 'Your email or IP address have been found on a spam blacklist.';
						break;
					}
				}
			}
		}
		
		if (empty($errors)){
			$message = implode("\n", array(
				'Message sent via vintagepantry.co.uk, to respond simply reply to this email.',
				'',
				'Details:',
				'  From: ' . $_POST['name'],
				'  E-Mail: ' . $_POST['email'],
				'',
				'=========================================================',
				'',
				$_POST['message'],
				'',
				'=========================================================',
			));
			
			$headers = implode("\r\n", array(
				'Content-Type: text/plain',
				'From: admin@xhcp.co.uk',
				'Reply-To: ' . $_POST['email'],
				'X-Mailer: vintagepantry.co.uk contact form',
			));
			
			mail('info@vintagepantry.co.uk', 'Message sent via vintagepantry.co.uk', $message, $headers);
		}
	}
}

if (isset($errors)){
	if (empty($errors)){
		echo '<div class="msg success">Your message has been sent.</div>';
	}else{
		foreach ($errors as $error){
			echo '<div class="msg error">', $error, '</div>';
		}
	}
}

?>
<form action="" method="post">
	<div>
		<label for="name">Name</label>
		<input type="text" class="text" name="name" id="name" value="<?php if (!empty($_POST['name'])) echo htmlentities($_POST['name']); ?>" />
	</div>
	<div>
		<label for="email">E-Mail Address</label>
		<input type="text" class="text" name="email" id="email" value="<?php if (!empty($_POST['email'])) echo htmlentities($_POST['email']); ?>" />
	</div>
	<div>
		<label for="message">Message</label>
		<textarea name="message" id="message" rows="10" cols="60"><?php if (!empty($_POST['message'])) echo htmlentities($_POST['message']); ?></textarea>
	</div>
	<div>
		<input type="submit" class="button" value="Send" />
	</div>
</form>
