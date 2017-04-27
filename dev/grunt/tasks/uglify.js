module.exports = {

	build: {

		options : {
			banner : '/*! <%= app.name %> Wordpress Plugin v<%= app.version %> */ \n'
			// preserveComments : 'some'
		},

		files: {

			'<%= app.jsPath %>/admin/datepicker.min.js': ['<%= app.jsPath %>/admin/datepicker.js'],
			'<%= app.jsPath %>/admin/sortable.min.js': ['<%= app.jsPath %>/admin/sortable.js'],
			'<%= app.jsPath %>/admin/repeatable.min.js': ['<%= app.jsPath %>/admin/repeatable.js'],
		}
	}
};