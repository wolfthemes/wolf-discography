module.exports = function(grunt) {
	grunt.registerTask( 'build', function() {
		grunt.task.run( [
			'compass',
			'autoprefixer',
			'cssmin',
			'markdown:log',
			'string-replace:build',
			'clean:build',
			'copyto:build',
			'replace:build',
			'makepot',
			'compress:build',
			'notify:build'
		] );
	} );
};