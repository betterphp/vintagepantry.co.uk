window.addEventListener('DOMContentLoaded', function(event){
	var links = document.getElementById('social').getElementsByTagName('a');
	
	for (var i = 0; i < links.length; ++i){
		links[i].addEventListener('click', function(event){
			window.open(this.href);
			event.preventDefault();
		}, false);
	}
}, false);