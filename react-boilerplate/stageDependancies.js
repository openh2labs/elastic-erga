var fs = require('fs');

var defaults = {
	bundleName : 'bundle.js',
	appendTo : 'body',
	css: 'styles.css'
};
var manifest;

function StageDependancies(config) {
	this.config = config;
}

StageDependancies.prototype.apply = function(compiler) {
	var config = this.config;

	config.basePath = config.basePath || defaults.basePath;
	config.bundleName = config.bundleName || defaults.bundleName; 
	config.css = config.css || defaults.css; 
	config.appendTo = config.appendTo || defaults.appendTo; 

	compiler.plugin('done', function() {
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
			manifest['terminalv2'] = {
				'basePath': require('./package.json').name + '/',
				'bundle': 'default', 
				'appendTo': config.appendTo
			};
		}
		else {
			manifest['terminalv2'] = {
				'basePath': require('./package.json').name + '/',
				'bundle': config.bundleName, 
				'appendTo': config.appendTo
			};
		}

		var data = JSON.stringify(manifest);
		fs.writeFile(config.manifestFile, data, (err) => {
		  if (err) throw err;
		});
	});
}

module.exports = StageDependancies;