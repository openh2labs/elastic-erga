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
	selectors = ['.','#'],
	basePath = 'build/js/components/';

	function loadJSON(callback) {   

	    var xobj = new XMLHttpRequest();
	        xobj.overrideMimeType('application/json');
	    xobj.open('GET', file, true);
	    xobj.onreadystatechange = function () {
	          if (xobj.readyState == 4 && xobj.status == '200') {
	            callback(JSON.parse(xobj.responseText));
	          }
	    };
	    xobj.send(null);  
	 }

	function loadScript(obj){
	    var script = document.createElement('script')
	    script.type = 'text/javascript';
	    script.src = [basePath, obj, '?jscachebuster=', new Date().getTime()].join('');
	    document.getElementsByTagName('body')[0].appendChild(script);
	}

	function loadCSS(obj){

		function createLinkElement(obj) {
			var css = document.createElement('link')
		    css.rel = 'stylesheet';
		    css.href = [basePath, obj.basePath, obj.url, '?csscachebuster=', new Date().getTime()].join('');
		    console.log(css.href);
		    document.getElementsByTagName('head')[0].appendChild(css);
		} 

		if(obj.bundle === 'default' && !(obj.css instanceof Array)){
			createLinkElement({url: obj.basePath + 'styles.css'});
		}
		else if (typeof obj.css == 'string') {
			createLinkElement({url: obj.basePath + obj.css});
		}
		else if (obj.css instanceof Array) {
			obj.css.forEach(function(name) {
				createLinkElement({url: obj.basePath + name});
			});
		}
	}

	function resolveComponents(json){
		manifest = json;
		var obj = manifest[location.pathname.substring(1)];
		if (typeof obj != 'undefined') {

			obj.forEach(function(component) {
				
				if (!document.querySelector(component.appendTo)) {

					var newElement = document.createElement('div');
					var appendTo = component.bundle !== 'default' && typeof component.appendTo == 'undefined' ? undefined : component.appendTo.substring(0, 1);

					switch(appendTo){
						case '.':
							newElement.className = component.appendTo.slice(1);
							break;
						case '#':
							newElement.id = component.appendTo.slice(1);
							break;
					}
					
					if(appendTo){
						document.getElementsByTagName('body')[0].appendChild(newElement);
					}
					else{
						console.warn('**** component at basePath', component.basePath, 'is missing associated appendTo property in manifest.json. ****')
					}
				}

				var jsPath = component.bundle === 'default' ? [component.basePath, 'bundle.js'].join('') : [component.basePath, component.bundle].join('');
				loadScript(jsPath);
				loadCSS(component);
			});
		}
	}

	try {
		loadJSON(resolveComponents);	
	}
	catch(e){
		console.error('manefist file may not exist. ', e);
	}
	
})();

