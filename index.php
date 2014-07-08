<?php

include('core/init.inc.php');

$page_title = 'The Vintage Pantry';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html version="-//W3C//DTD XHTML 1.1//EN" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.w3.org/1999/xhtml http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="ext/img/favicon.ico" />
		<link rel="stylesheet" media="all" type="text/css" href="ext/css/build/style.min.css" />
		<script type="text/javascript" src="ext/jsc/lib.js"></script>
		<script type="text/javascript" src="ext/jsc/header.js"></script>
		<?php
		
		$page_script = "ext/jsc/{$_GET['page']}.page.js";
		
		if (file_exists($page_script)){
			echo '<script type="text/javascript" src="', $page_script, '"></script>';
		}
		
		?>
		{OG_META_TAGS}
		<title>{PAGE_TITLE}</title>
	</head>
	<body>
		<div id="nav-bg">&nbsp;</div>
		<div id="wrap">
			<div id="logo">
				<img src="ext/img/logo_banner.png" alt="" />
			</div>
			<div id="nav">
				<a href="shop.html">Shop</a>
				<a href="about.html">About</a>
				<a href="contact.html">Contact</a>
				<?php
				
				if (isset($_SESSION['user'])){
					echo '<a href="admin.html">Admin</a>';
				}
				
				?>
			</div>
			<div id="social">
				<a href="http://www.facebook.com/TheVintagePantry" title="Facebook"><img src="ext/img/facebook_ico.png" alt="Facebook" /></a>
				<a href="https://twitter.com/Vintage_Pantry" title="Twitter"><img src="ext/img/twitter_ico.png" alt="Twitter" /></a>
				<a href="http://pinterest.com/vintagepantry" title="Pinterest"><img src="ext/img/pinterest_ico.png" alt="Pinterest" /></a>
				<a href="http://stores.ebay.co.uk/The-Vintage-Kitchen-Pantry" title="eBay Shop"><img src="ext/img/ebay_ico.png" alt="eBay" /></a>
			</div>
			<div id="content">
				<?php include($include_file); ?>
			</div>
			<div id="copy">&copy; 2012 The Vintage Pantry | <a href="terms.html" title="View our terms &amp; conditions">Terms &amp; conditions</a></div>
		</div>
		<div id="blank">&nbsp;</div>
		
		<div id="loading"><img src="ext/img/loading.gif" alt="loading..." /></div>
		<div id="image-box">
			<img id="image-box-image" src="" alt="" />
			<img id="image-box-close" src="ext/img/close_ico.png" alt="close" title="Press ESC to close" />
		</div>
	</body>
</html>
<?php

$meta = '';

if (isset($og_vars)){
	foreach ($og_vars as $key => $value){
		$meta .= '<meta property="og:' . htmlentities($key) . '" content="' . htmlentities($value) . '" />';
	}
}

$output = ob_get_contents();
$output = str_replace(array('{PAGE_TITLE}', '{OG_META_TAGS}'), array(htmlentities($page_title), $meta), $output);

ob_clean();

echo $output;

?>
