/*
*	The purpose of the is file is to do the following:
*		- create, update manifest.json file which specifies the dependencies for the terminal component
*		- create, update directory for the bootstrap file
*/

var fs = require('fs');
var mkdirp = require('mkdirp');

var defaults = {
	bundleName : 'bundle.js',
	appendTo : 'body',
	css: 'styles.css',
	destination: '../laravel/public/js'
};
var manifest;

function DeployToPublicWebFolder(config) {
	this.config = config;
}

// creates, updates manifest.json file
function modifyManifest(config) {
	if (typeof config.manifestFile == 'undefined') {
			console.log('Error Missing Manifest: manifest file needs to be defined in webpack config!');
			return;
		}

		try {
			manifest = require(config.manifestFile);
		}catch(e) {
			manifest = {};
		}

		if (config.default) {
			manifest[config.urlMapping] = {
				'basePath': require('../package.json').name + '/',
				'bundle': 'default', 
				'appendTo': config.appendTo
			};
		}
		else {
			manifest[config.urlMapping] = {
				'basePath': require('../package.json').name + '/',
				'bundle': config.bundleName, 
				'appendTo': config.appendTo
			};
		}

		var data = JSON.stringify(manifest);
		fs.writeFile(config.manifestFile, data, (err) => {
		  if (err) throw err;
		});
}

DeployToPublicWebFolder.prototype.apply = function(compiler) {
	var config = this.config;

	if (typeof config.urlMapping === 'string') {
		config.basePath = config.basePath || defaults.basePath;
		config.bundleName = config.bundleName || defaults.bundleName; 
		config.css = config.css || defaults.css; 
		config.appendTo = config.appendTo || defaults.appendTo; 
		config.dest = config.destination || defaults.destination;

		compiler.plugin('done', function() {
			

			modifyManifest(config);
			
			mkdirp(config.dest, function(){});
			fs.createReadStream('./shared/bootstrap-components.js').pipe(fs.createWriteStream([config.dest,'/bootstrap-components.js'].join('')));
		});
	}else{
		console.log('Warning! DeployToPublicWebFolder requires urlMapping property to be set to the relative url fragment for the component.')
	}
}

module.exports = DeployToPublicWebFolder;