/*
*	This bootstrap script is to do the following:
*	
*		1.	load a manifest file that specifies which components to be used on a given page, and the css selector to identify the element
*		2.	resolve the elements existance. If it does not exist to create it on the page
*		3. 	load the components, js by creating a script tag with its source set and appending to the body tag 
*	
*		manifest object pattern:
*		
*			{
*				'page' : [
*					{ bundle: 'my-component-1/build/my-component-1.js', appendTo: '.div1'},
*					{ bundle: 'my-component-2/build/my-component-2.js', appendTo: '.div2'}
*				],
*				'page2' : [
*					{ bundle: 'my-component-2/build/my-component-2.js', appendTo: '.div3'}
*				]
*			}
*/

(function(){

	var manefest = {}, 
	file = MAIFEST_FILE_PATH || undefined,
	selectors = ['.','#'];

	function loadJSON(callback) {   

	    var xobj = new XMLHttpRequest();
	        xobj.overrideMimeType("application/json");
	    xobj.open('GET', file, true);
	    xobj.onreadystatechange = function () {
	          if (xobj.readyState == 4 && xobj.status == "200") {
	            callback(JSON.parse(xobj.responseText));
	          }
	    };
	    xobj.send(null);  
	 }

	function loadScript(url){
	    var script = document.createElement("script")
	    script.type = "text/javascript";
	    script.src = url;
	    document.getElementsByTagName("body")[0].appendChild(script);
	}

	function resolveComponents(json){
		manifest = json;
		var obj = manifest[location.pathname.substring(1)];

		if (typeof obj != 'undefined') {
			obj.forEach(function(component) {
				
				if (!document.querySelector(component.appendTo)) {

					var newElement = document.createElement('div');

					switch(component.appendTo.substring(1)){
						case '.':
							newElement.className = component.appendTo.slice(1);
							break;
						case '#':
							newElement.id = component.appendTo.slice(1);
					}
					
					document.getElementsByTagName('body')[0].appendChild(newElement);
				} 

				loadScript(component.bundle);
			});
		}
	}

	try {
		loadJSON(resolveComponents);	
	}
	catch(e){
		console.error('manefest file may not exist. ', e);
	}
	
})();

