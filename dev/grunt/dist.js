module.exports = function(grunt) {

	grunt.registerTask( 'dist', function() {
		grunt.task.run( [
			'rsync:dist',
			'notify:dist'
		] );
	} );
};