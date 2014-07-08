window.addEventListener('DOMContentLoaded', function(event){
	var preview = document.getElementById('preview_image');
	var previewLink = document.getElementById('preview_link');
	
	var thumbLinks = previewLink.nextElementSibling.getElementsByTagName('a');
	
	var handleClick = function(event){
		var previewPath = this.getAttribute('data-preview-path');
		var fullPath = this.getAttribute('data-full-path');
		
		if (previewPath && fullPath){
			preview.src = previewPath;
			previewLink.href = fullPath;
			event.preventDefault();
		}
	};
	
	for (var i = 0; i < thumbLinks.length; ++i){
		thumbLinks[i].addEventListener('click', handleClick, false);
	}
	
	previewLink.addEventListener('click', function(event){
		var blank = document.getElementById('blank');
		var loading = document.getElementById('loading');
		var imageBox = document.getElementById('image-box');
		var imageBoxImage = document.getElementById('image-box-image');
		
		event.preventDefault();
		
		blank.style.display = 'block';
		loading.style.display = 'block';
		
		imageBoxImage.addEventListener('load', function(event){
			loading.style.display = 'none';
			imageBox.style.display = 'block';
			
			var size = getWindowSize();
			
			imageBoxImage.style.maxWidth = (size.width - 60) + 'px';
			imageBoxImage.style.maxHeight = (size.height - 60) + 'px';
			
			imageBox.style.marginLeft = '-' + (imageBox.scrollWidth / 2) + 'px';
			imageBox.style.marginTop = '-' + (imageBox.scrollHeight / 2) + 'px';
		}, false);
		
		imageBoxImage.src = this.href;
	}, false);
	
	window.addEventListener('click', function(event){
		var blank = document.getElementById('blank');
		var loading = document.getElementById('loading');
		var imageBox = document.getElementById('image-box');
		
		if (blank.style.display == 'block' && loading.style.display != 'block'){
			blank.style.display = 'none';
			loading.style.display = 'none';
			imageBox.style.display = 'none';
		}
	}, false);
	
	document.addEventListener('keyup', function(event){
		var key = (typeof event.keyCode == 'number') ? event.keyCode : event.which;
		
		if (key == 27){
			var blank = document.getElementById('blank');
			var loading = document.getElementById('loading');
			var imageBox = document.getElementById('image-box');
			
			if (blank.style.display == 'block' && loading.style.display != 'block'){
				blank.style.display = 'none';
				loading.style.display = 'none';
				imageBox.style.display = 'none';
			}
		}
	}, false);
}, false);
