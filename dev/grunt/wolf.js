module.exports = function(grunt) {

	grunt.registerTask( 'wolf', function() {
		grunt.task.run( [
			'rsync:wolf',
			'notify:wolf'
		] );
	} );
};