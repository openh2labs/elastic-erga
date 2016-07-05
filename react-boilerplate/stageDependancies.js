var fs = require('fs');

var defaults = {
	basePath : '../components',
	bundleName : 'bundle.js',
	appendTo : 'body'
};
var manifestFile = '../components/manifest.json';

function StageDependancies(config) {
	this.config = config;
}

StageDependancies.prototype.apply = function(compiler) {
	var config = this.config;

	config.basePath = config.basePath || defaults.basePath;
	config.bundleName = config.bundleName || defaults.bundleName; 
	config.appendTo = config.appendTo || defaults.appendTo; 

	compiler.plugin('done', function() {
		var d = new Date();
		var manifest = require(manifestFile);
		manifest['terminal'] = {
			'bundle': [config.basePath, config.bundleName].join('/'), 
			'appendTo': config.appendTo
		};
		var data = JSON.stringify(manifest);
		fs.writeFile(manifestFile, data, (err) => {
		  if (err) throw err;
		});
	});
}

module.exports = StageDependancies;