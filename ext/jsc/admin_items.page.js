window.addEventListener('DOMContentLoaded', function(event){
	document.getElementById('add_another_button').addEventListener('click', function(event){
		var form = document.getElementById('item_form');
		var sections = document.querySelectorAll('#item_form > div');
		var template = sections[0].cloneNode(true);
		
		var title = template.getElementsByTagName('h3')[0];
		title.removeChild(title.firstChild);
		title.appendChild(document.createTextNode('Item ' + sections.length));
		
		var inputs = template.getElementsByTagName('input');
		
		for (var i = 0; i < inputs.length; ++i){
			if (inputs[i].type == 'text'){
				inputs[i].value = '';
			}else if (inputs[i].type == 'file'){
				try{
					inputs[i].value = '';
					
					if (inputs[i].value){
						inputs[i].type = 'text';
						inputs[i].type = 'file';
					}
				}catch (e){}
			}
			
			if (inputs[i].name){
				inputs[i].name = inputs[i].name.replace('[0]', '[' + (sections.length - 1) + ']');
			}
		}
		
		var textareas = template.getElementsByTagName('textarea');
		
		for (var i = 0; i < textareas.length; ++i){
			textareas[i].value = '';
			textareas[i].name = textareas[i].name.replace('[0]', '[' + (sections.length - 1) + ']');
		}
		
		var selects = template.getElementsByTagName('select');
		
		for (var i = 0; i < selects.length; ++i){
			selects[i].selectedIndex = 0;
			selects[i].name = selects[i].name.replace('[0]', '[' + (sections.length - 1) + ']');
		}
		
		var lastElement = form.lastChild;
		
		while (lastElement && lastElement.nodeType !== 1){
			lastElement = lastElement.previousSibling;
		}
		
		form.insertBefore(document.createElement('hr'), lastElement);
		form.insertBefore(template, lastElement);
		
		template.scrollIntoView(true);
		inputs[0].focus();
	}, false);
	
	document.getElementById('item_form').addEventListener('submit', function(event){
		var sections = document.querySelectorAll('#item_form > div');
		
		for (var i = 0; i < sections.length - 1; ++i){
			var inputs = sections[i].getElementsByTagName('input');
			var selects = sections[i].getElementsByTagName('select');
			
			for (var c = 0; c < inputs.length; ++c){
				if (inputs[c].type == 'text'){
					if (inputs[c].value == ''){
						alert('Empty field for item ' + (i + 1));
						event.preventDefault();
						inputs[c].focus();
						return;
					}
				}else if (inputs[c].type == 'file'){
					if (inputs[c].value == ''){
						alert('No images selected for item ' + (i + 1));
						event.preventDefault();
						return;
					}
				}
			}
			
			for (var c = 0; c < selects.length; ++c){
				if (selects[c].selectedIndex == 0){
					alert('No category selected for item ' + (i + 1));
					event.preventDefault();
					return;
				}
			}
		}
	}, false);
}, true);