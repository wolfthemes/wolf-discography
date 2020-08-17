module.exports = function(grunt) {
	/**
	 * Zip theme
	 */
	grunt.registerTask( 'doc', function() {
		grunt.task.run( [
			'string-replace:build',
			'replace:doc',
			'rsync:doc',
			'notify:doc'
		] );
	} );
};