module.exports = {

	options : {
		jshintrc : '.jshintrc'
	},

	all: [
		'<%= app.jsPath %>/*.js',
		'<%= app.jsPath %>/admin/*.js',
		'!<%= app.jsPath %>/lib/**',
		'!<%= app.jsPath %>/admin/min/**'
	]
};