<?php

if (isset($_GET['action'])){
	switch($_GET['action']){
		case 'add_category':
			if (empty($_POST['category_name'])){
				$errors[] = 'You must enter a category name.';
			}
			
			if (empty($errors)){
				category::add_new($_POST['category_name']);
			}
		break;
		case 'remove_category':
			if (empty($_GET['category_id'])){
				$errors[] = 'You must specify a category.';
			}else if (($category = category::fetch_by_id($_GET['category_id'])) === false){
				$errors[] = 'That category does not exist.';
			}
			
			if (empty($errors)){
				$category->set_removed(true);
			}
		break;
		default:
			$errors[] = 'Invalid action.';
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
<h1>Shop Categories</h1>
<table>
	<tr>
		<th>Name</th>
		<th>&nbsp;</th>
	</tr>
	<?php
	
	foreach (category::fetch_all() as $category){
		if (!$category->is_removed()){
			?>
			<tr>
				<td><?php echo $category->get_name(); ?></td>
				<td>
					[<a href="?action=remove_category&amp;category_id=<?php echo $category->get_id(); ?>">remove</a>]
					[<a href="admin_edit_category.html?category_id=<?php echo $category->get_id(); ?>">edit</a>]
				</td>
			</tr>
			<?php
		}
	}
	
	?>
</table>

<h2>Add Category</h2>
<form action="?action=add_category" method="post">
	<div>
		<label for="category_name">Name</label>
		<input type="text" class="text" name="category_name" id="category_name" />
	</div>
	<div>
		<input type="submit" class="button" value="Add Category" />
	</div>
</form>