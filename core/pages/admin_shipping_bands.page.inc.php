<?php

if (empty($_GET['destination_id']) || ($destination = shipping_destination::fetch_by_id($_GET['destination_id'])) === false){
	redirect('404.html', '404 Not Found');
}

if (isset($_GET['action'])){
	switch ($_GET['action']){
		case 'add_band':
			if (!isset($_POST['min_weight'], $_POST['max_weight'], $_POST['price'])){
				$errors[] = 'Invalid request.';
			}
			
			if ($_POST['min_weight'] >= $_POST['max_weight']){
				$errors[] = 'The min weight must be less than the max weight.';
			}
			
			if (empty($errors)){
				$destination->add_band($_POST['min_weight'], $_POST['max_weight'], $_POST['price']);
			}
		break;
		case 'remove_band':
			if (empty($_GET['band_id'])){
				$errors[] = 'You must specify a band to remove.';
			}else if (($band = shipping_band::fetch_by_id($_GET['band_id'])) === false){
				$errors[] = 'No such band.';
			}
			
			if (empty($errors)){
				$band->remove();
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
<h1><?php echo $destination->get_name(); ?> Shipping Bands</h1>
<table>
	<tr>
		<th>Min Weight (g)</th>
		<th>Max Weight (g)</th>
		<th>Price</th>
		<th>&nbsp;</th>
	</tr>
	<?php
	
	foreach ($destination->fetch_bands() as $band){
		?>
		<tr>
			<td><?php echo $band->get_min_weight(); ?></td>
			<td><?php echo $band->get_max_weight(); ?></td>
			<td><?php echo money_format('%.2i', $band->get_price()); ?></td>
			<td>
				[<a href="?destination_id=<?php echo $destination->get_id(); ?>&amp;action=remove_band&amp;band_id=<?php echo $band->get_id(); ?>">remove</a>]
			</td>
		</tr>
		<?php
	}
	
	?>
</table>

<h2>Add Band</h2>
<form action="?destination_id=<?php echo $destination->get_id(); ?>&amp;action=add_band" method="post">
	<div>
		<label for="min_weight">Min Weight (g)</label>
		<input type="text" class="text" name="min_weight" id="min_weight" />
	</div>
	<div>
		<label for="max_weight">Max Weight (g)</label>
		<input type="text" class="text" name="max_weight" id="max_weight" />
	</div>
	<div>
		<label for="price">Price</label>
		<input type="text" class="text" name="price" id="price" />
	</div>
	<div>
		<input type="submit" class="button" value="Add Band" />
	</div>
</form>