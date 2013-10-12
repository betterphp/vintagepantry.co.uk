<?php

if (isset($_POST['titles'], $_POST['category_ids'], $_POST['prices'], $_POST['weights'], $_POST['quantities'], $_POST['descriptions'], $_FILES['images']['name'])){
	$total_items = count($_POST['titles']);
	
	set_time_limit(0);
	
	for ($i = 0; $i < $total_items; ++$i){
		$title = $_POST['titles'][$i];
		$category_id = $_POST['category_ids'][$i];
		$price = $_POST['prices'][$i];
		$weight = $_POST['weights'][$i];
		$quantity = $_POST['quantities'][$i];
		$description = trim($_POST['descriptions'][$i]);
		$images = $_FILES['images']['tmp_name'][$i];
		
		$item = item::add_new(new category($category_id, '', false), $title, $description, $price, $weight, $quantity);
		$item->set_images($images);
	}
}

?>
<ul class="admin-navigation">
	<li><a href="admin.html">View Sales</a></li>
	<li><a href="admin_categories.html">Manage shop categories</a></li>
	<li><a href="admin_shipping_destinations.html">Manage shipping bands</a></li>
	<li><a href="admin_items.html">Manage Items</a></li>
</ul>
<h1>Shop Items</h1>
<table>
	<tr>
		<th>Title</th>
		<th>Category</th>
		<th>Price</th>
		<th>Weight</th>
		<th>Quantity</th>
		<th>&nbsp;</th>
	</tr>
	<?php
	
	foreach (item::fetch_all() as $item){
		?>
		<tr>
			<td><a href="view_item.html?item_id=<?php echo $item->get_id(); ?>"><?php echo htmlentities($item->get_title()); ?></a></td>
			<td><?php echo htmlentities($item->get_category()->get_name()); ?></td>
			<td><?php echo $item->get_price(); ?></td>
			<td><?php echo $item->get_weight(); ?>g</td>
			<td><?php echo $item->get_quantity(); ?></td>
			<td>
				[<a href="admin_edit_item.html?item_id=<?php echo $item->get_id(); ?>">edit</a>]
			</td>
		</tr>
		<?php
	}
	
	?>
</table>

<h2>Add New Items</h2>
<form action="" method="post" enctype="multipart/form-data" id="item_form">
	<div>
		<h3>Item 1</h3>
		<div>
			<label>Title</label>
			<input type="text" class="text" name="titles[0]" />
		</div>
		<div>
			<label>Category</label>
			<select name="category_ids[0]">
				<option>---</option>
				<?php
				
				foreach (category::fetch_all() as $category){
					if (!$category->is_removed()){
						echo '<option value="', $category->get_id(), '">', $category->get_name(), '</option>';
					}
				}
				
				?>
			</select>
		</div>
		<div>
			<label>Price</label>
			<input type="text" class="text" name="prices[0]" />
		</div>
		<div>
			<label>Weight</label>
			<input type="text" class="text" name="weights[0]" />
		</div>
		<div>
			<label>Quantity</label>
			<input type="text" class="text" name="quantities[0]" />
		</div>
		<div>
			<label>Images</label>
			<input type="file" multiple="multiple" name="images[0][]" />
		</div>
		<div>
			<label>Description</label>
			<textarea rows="10" cols="60" name="descriptions[0]"></textarea>
		</div>
	</div>
	
	<div>
		<input type="button" class="button" id="add_another_button" value="Add Another" />
		<input type="submit" class="button" value="Post All" />
	</div>
</form>
