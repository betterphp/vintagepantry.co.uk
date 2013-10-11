<?php

$category_id = (!empty($_GET['category_id'])) ? intval($_GET['category_id']) : null;
$search_term = (!empty($_GET['search_term'])) ? $_GET['search_term'] : null;

$categories = category::fetch_all();
$items = item::fetch_all($category_id, $search_term);

?>
<div class="shop-filter">
	<form action="" method="get">
		<div>
			<?php
			
			if ($category_id != null){
				echo '<input type="hidden" name="category_id" value="', $category_id, '" />';
			}
			
			?>
			<input type="text" class="text" name="search_term" value="<?php if ($search_term != null) echo htmlentities($search_term); ?>" />
			<input type="submit" class="button" value="Search" />
		</div>
	</form>
	
	<ul>
		<?php
		
		foreach ($categories as $category){
			if (!$category->is_removed()){
				if ($category_id == $category->get_id()){
					echo '<li><a href="?category_id=', $category->get_id(), '" class="current-page">', htmlentities($category->get_name()), '</a></li>';
				}else{
					echo '<li><a href="?category_id=', $category->get_id(), '">', htmlentities($category->get_name()), '</a></li>';
				}
			}
		}
		
		?>
	</ul>
</div>

<div class="shop-items">
	<?php
	
	if (empty($items)){
		echo '<div class="msg error">There are no products to display.</div>';
	}
	
	foreach ($items as $item){
		?>
		<div class="shop-product">
			<div><a href="view_item.html?item_id=<?php echo $item->get_id(); ?>"><img src="<?php echo $item->get_images()[0]->get_cover_relative_path(); ?>" alt="<?php echo htmlentities($item->get_title()); ?>" /></a></div>
			<h3><a href="view_item.html?item_id=<?php echo $item->get_id(); ?>" title="View more details on this item"><?php echo htmlentities($item->get_title()); ?></a></h3>
			<div>£<?php echo money_format('%.2i', $item->get_price()); ?></div>
		</div>
		<?php
	}
	
	?>
</div>