<?php

if (isset($_GET['action'])){
	switch ($_GET['action']){
		case 'add_destination':
			if (empty($_POST['destination_name'])){
				$errors[] = 'You must enter a destination name.';
			}
			
			if (empty($errors)){
				shipping_destination::add_new($_POST['destination_name']);
			}
		break;
		case 'remove_destination':
			if (empty($_GET['destination_id'])){
				$errors[] = 'Invalid ID.';
			}else if (($destination = shipping_destination::fetch_by_id($_GET['destination_id'])) === false){
				$errors[] = 'No such destination.';
			}
			
			if (empty($errors)){
				$destination->remove();
			}
		break;
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
<h1>Shipping Destinations</h1>
<table>
	<tr>
		<th>Name</th>
		<th>&nbsp;</th>
	</tr>
	<?php
	
	foreach (shipping_destination::fetch_all() as $destination){
		?>
		<tr>
			<td><?php echo htmlentities($destination->get_name()); ?></td>
			<td>
				[<a href="?action=remove_destination&amp;destination_id=<?php echo $destination->get_id(); ?>">remove</a>]
				[<a href="admin_edit_shipping_destination.html?destination_id=<?php echo $destination->get_id(); ?>">edit</a>]
				[<a href="admin_shipping_bands.html?destination_id=<?php echo $destination->get_id(); ?>">price bands</a>]
			</td>
		</tr>
		<?php
	}
	
	?>
</table>

<h2>Add Destination</h2>
<form action="?action=add_destination" method="post">
	<div>
		<label for="destination_name">Name</label>
		<input type="text" class="text" name="destination_name" id="destination_name" />
	</div>
	<div>
		<input type="submit" class="button" value="Add Destination" />
	</div>
</form>