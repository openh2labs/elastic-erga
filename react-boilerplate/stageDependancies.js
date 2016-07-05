var fs = require('fs');

var defaults = {
	bundleName : 'bundle.js',
	appendTo : 'body',
	css: 'styles.css'
};
var manifestFile = '../components/manifest.json';

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
		var manifest = require(manifestFile);

		if (config.default) {
			manifest['terminal'] = {
				'basePath': require('./package.json').name,
				'bundle': 'default', 
				'appendTo': config.appendTo
			};
		}
		else {
			manifest['terminal'] = {
				'basePath': require('./package.json').name,
				'bundle': config.bundleName, 
				'appendTo': config.appendTo
			};
		}

		var data = JSON.stringify(manifest);
		fs.writeFile(manifestFile, data, (err) => {
		  if (err) throw err;
		});
	});
}

module.exports = StageDependancies;