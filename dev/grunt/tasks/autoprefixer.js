module.exports = {
	options: {
		browsers: ['last 3 versions', 'bb 10', 'android 3', 'ie 8']
	},
	no_dest: {
		src: '<%= app.cssPath %>/*.css',
	}
};