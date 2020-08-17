module.exports = function(grunt) {

	grunt.registerTask( 'help', function() {
		grunt.task.run( [
			'rsync:help',
			'notify:help'
		] );
	} );
};