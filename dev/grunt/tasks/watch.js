module.exports = {

	js:{
		files:[
			'<%= app.jsPath %>/*.js', '!<%= app.jsPath %>/lib/**',
			'<%= app.jsPath %>/admin/*.js', '!<%= app.jsPath %>/admin/min/**',
			//'<%= app.jsPath %>/wp-wolf-framework/assets/js/src/*.js',
			],
		tasks: [
			//'jshint',
			//'uglify:admin',
			//'notify:js'
		]
	},


	sass: {
		files: [
			'<%= app.scssPath %>/*.scss',
			'<%= app.scssAdminPath %>/*.scss'
		],
		tasks: [
			'compass',
			'autoprefixer',
			'cssmin',
			'notify:scss'
		],
	},

	css: {
		files: ['*.css']
	},

	livereload: {
		files: [ '<%= app.cssPath %>/*.css', '<%= app.cssPath %>/admin/*.css' ],
		options: { livereload: true }
	}
};