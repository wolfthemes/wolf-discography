module.exports = function(grunt) {
	grunt.registerTask( 'bower', function() {
		grunt.task.run( [
			'uglify:bower',
			'cssmin:bower',
			'copy:animatecss',
			'copy:flexsliderJs',
			'copy:flexsliderFonts',
			'copy:wow',
			'copy:waypoints',
			'copy:swipeboxCss',
			'copy:swipeboxJs',
			'copy:swipeboxImg',
			//'copy:imagesloaded',
			//'copy:packery',
			//'copy:isotope',
			'copy:owlCarouselJs',
			'copy:owlCarouselCss',
			'copy:counterup'
		] );
	} );
};