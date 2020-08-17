module.exports = function(grunt) {
	grunt.registerTask( 'write', function() {
		grunt.task.run( [
			'markdown:log',
			'string-replace',
			'replace:doc'
		] );
	} );
};