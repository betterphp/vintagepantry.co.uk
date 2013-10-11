<?php

if (empty($_GET['category_id']) || ($category = category::fetch_by_id($_GET['category_id'])) === false){
	redirect('404.html', '404 Not Found');
}

if (isset($_POST['category_name'])){
	if (empty($_POST['category_name'])){
		$errors[] = 'You must enter a category name.';
	}
		
	if (empty($errors)){
		$category->set_name($_POST['category_name']);
		redirect('admin_categories.html');
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
<h1><?php echo htmlentities($category->get_name()); ?> Category</h1>
<form action="" method="post">
	<div>
		<label for="category_name">Name</label>
		<input type="text" class="text" name="category_name" id="category_name" value="<?php echo htmlentities((isset($_POST['category_name'])) ? $_POST['category_name'] : $category->get_name());  ?>" />
	</div>
	<div>
		<input type="submit" class="button" value="Update Category" />
	</div>
</form>