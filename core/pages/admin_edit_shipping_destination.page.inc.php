<?php

if (empty($_GET['destination_id']) || ($destination = shipping_destination::fetch_by_id($_GET['destination_id'])) === false){
	redirect('404.html', '404 Not Found');
}

if (isset($_POST['destination_name'])){
	if (empty($_POST['destination_name'])){
		$errors[] = 'You must enter a name.';
	}
	
	if (empty($errors)){
		$destination->set_name($_POST['destination_name']);
	}
}

if (!empty($errors)){
	foreach ($errors as $error){
		echo '<div class="msg error">', $error, '</div>';
	}
}

?>
<ul class="admin-navigation">
	<li><a href="admin.html">View Sales</a></li>
	<li><a href="admin_categories.html">Manage shop categories</a></li>
	<li><a href="admin_shipping_destinations.html">Manage shipping bands</a></li>
	<li><a href="admin_items.html">Manage Items</a></li>
</ul>
<h1>Edit <?php echo $destination->get_name(); ?></h1>
<form action="" method="post">
	<div>
		<label for="destination_name">Name</label>
		<input type="text" class="text" name="destination_name" id="destination_name" value="<?php echo htmlentities((isset($_POST['destination_name'])) ? $_POST['destination_name'] : $destination->get_name()); ?>" />
	</div>
	<div>
		<input type="submit" class="button" value="Update Destination" />
	</div>
</form>