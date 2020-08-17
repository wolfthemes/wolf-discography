module.exports = function(grunt) {

	// load dependencies
	require('load-grunt-tasks')(grunt);

	function loadConfig(path) {
		var glob = require('glob');
		var object = {};
		var key;

		glob.sync('*', {cwd: path}).forEach(function(option) {
			key = option.replace(/\.js$/,'');
			object[key] = require(path + option);
		});

		return object;
	}

	var config = {
		pkg : grunt.file.readJSON('package.json'),
		app : grunt.file.readJSON('app.config.json'),
		version : grunt.option('target') || grunt.file.readJSON('app.config.json').version,
		link : grunt.file.readJSON('app.config.json').link || 'http://wlfthm.es/mPJRk8',
		shortlink : grunt.file.readJSON('app.config.json').shortlink || grunt.file.readJSON('app.config.json').link || 'http://wolfthemes.com/wordpress-plugins',
		news : grunt.file.read('../html/news.html'),
		info : grunt.file.read('../html/info.html'),
		warning : grunt.file.read('../html/warning.html')
	};

	grunt.util._.extend(config, loadConfig('./grunt/tasks/'));

	grunt.initConfig(config);

	grunt.loadTasks('grunt');

	grunt.registerTask('default', function() {
		// grunt.log.writeln("Hello world!");
		grunt.task.run( [
			'compass',
			'autoprefixer',
			'cssmin',
			// 'jshint',
			'uglify',
		] );
	});

	grunt.registerTask('dev', function() {
		grunt.task.run( [
			'watch'
		] );
	});

	grunt.registerTask('debug', function() {
		//grunt.log.writeln(grunt.file.read('../news.html'));
	});
};