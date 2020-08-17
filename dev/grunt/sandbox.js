module.exports = function(grunt) {

	grunt.registerTask( 'sandbox', function() {
		grunt.task.run( [
			'rsync:sandbox',
			'notify:prod'
		] );
	} );
};