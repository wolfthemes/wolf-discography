module.exports = function(grunt) {

	grunt.registerTask( 'prod', function() {
		grunt.task.run( [
			'rsync:demo',
			// 'rsync:wolf',
			// 'rsync:help',
			'notify:prod'
		] );
	} );
};