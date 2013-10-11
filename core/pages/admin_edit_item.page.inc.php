<?php

if (empty($_GET['item_id']) || ($item = item::fetch_by_id($_GET['item_id'])) === false){
	redirect('404.html', '404 Not Found');
}

if (isset($_POST['title'], $_POST['category_id'], $_POST['price'], $_POST['weight'], $_POST['quantity'], $_POST['description'])){
	$errors = array();
	
	if (empty($_POST['title'])){
		$errors[] = 'You must enter a title.';
	}
	
	if (empty($_POST['category_id']) || ($category = category::fetch_by_id($_POST['category_id'])) === false){
		$errors[] = 'You must select a valid category.';
	}
	
	if (empty($_POST['price']) || $_POST['price'] <= 0){
		$errors[] = 'The item price must be positive.';
	}
	
	if (empty($_POST['weight']) || $_POST['weight'] <= 0){
		$errors[] = 'The item weight must be positive.';
	}
	
	if ($_POST['quantity'] < 0){
		$errors[] = 'The item quanity must not be negative.';
	}
	
	if (empty($_POST['description'])){
		$errors[] = 'You must enter a description.';
	}
	
	if (empty($errors)){
		$item->update_details($_POST['title'], $category, $_POST['price'], $_POST['weight'], $_POST['quantity'], $_POST['description']);
	}
}

if (isset($errors)){
	if (empty($errors)){
		echo '<div class="msg success">Item updated</div>';
	}else{
		foreach ($errors as $error){
			echo '<div class="msg error">', $error, '</div>';
		}
	}
}

?>
<ul class="admin-navigation">
	<li><a href="admin.html">View Sales</a></li>
	<li><a href="admin_categories.html">Manage shop categories</a></li>
	<li><a href="admin_shipping_destinations.html">Manage shipping bands</a></li>
	<li><a href="admin_items.html">Manage Items</a></li>
</ul>
<h1>Edit <?php echo htmlentities($item->get_title()); ?></h1>
<form action="" method="post" enctype="multipart/form-data" id="item_form">
	<div>
		<label>Title</label>
		<input type="text" class="text" name="title" value="<?php echo htmlentities((isset($_POST['title'])) ? $_POST['title'] : $item->get_title()); ?>" />
	</div>
	<div>
		<label>Category</label>
		<select name="category_id">
			<?php
			
			$selected_category_id = (isset($_POST['category_id'])) ? intval($_POST['category_id']) : $item->get_category()->get_id();
			
			foreach (category::fetch_all() as $category){
				if (!$category->is_removed()){
					if ($category->get_id() == $selected_category_id){
						echo '<option value="', $category->get_id(), '" selected="selected">', $category->get_name(), '</option>';
					}else{
						echo '<option value="', $category->get_id(), '">', $category->get_name(), '</option>';
					}
				}
			}
			
			?>
		</select>
	</div>
	<div>
		<label>Price</label>
		<input type="text" class="text" name="price" value="<?php echo htmlentities((isset($_POST['price'])) ? $_POST['price'] : $item->get_price()); ?>" />
	</div>
	<div>
		<label>Weight</label>
		<input type="text" class="text" name="weight" value="<?php echo htmlentities((isset($_POST['weight'])) ? $_POST['weight'] : $item->get_weight()); ?>" />
	</div>
	<div>
		<label>Quantity</label>
		<input type="text" class="text" name="quantity" value="<?php echo htmlentities((isset($_POST['quantity'])) ? $_POST['quantity'] : $item->get_quantity()); ?>" />
	</div>
	<div>
		<label>Description</label>
		<textarea rows="10" cols="60" name="description"><?php echo htmlentities((isset($_POST['description'])) ? $_POST['description'] : $item->get_description()); ?></textarea>
	</div>
	<div>
		<input type="submit" class="button" value="Update Item" />
	</div>
</form>