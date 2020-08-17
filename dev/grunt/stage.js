module.exports = function(grunt) {

	grunt.registerTask( 'stage', function() {
		grunt.task.run( [
			'rsync:stage',
			'notify:stage'
		] );
	} );
};