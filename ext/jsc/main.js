function getWindowSize(){
	if (window.innerWidth && window.innerHeight){
		return {
			width: window.innerWidth,
			height: window.innerHeight
		};
	}else if (document.body && document.body.offsetWidth){
		return {
			width: document.body.offsetWidth,
			height: document.body.offsetHeight
		};
	}else if (document.documentElement && document.documentElement.offsetWidth){
		return {
			width: document.documentElement.offsetWidth,
			height: document.documentElement.offsetHeight
		};
	}else{
		return {
			width: 800,
			height: 600
		};
	}
}

window.addEventListener('DOMContentLoaded', function(event){
	var links = document.getElementById('social').getElementsByTagName('a');
	
	var handleClick = function(event){
		window.open(this.href);
		event.preventDefault();
	};
	
	for (var i = 0; i < links.length; ++i){
		links[i].addEventListener('click', handleClick, false);
	}
}, false);
