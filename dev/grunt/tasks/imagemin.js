module.exports = {

	assets: {                       
		files: [{
			expand: true,
			cwd: '<%= app.imagesPath %>/',
			src: ['**/*.{png,jpg,gif}'],
			dest: '<%= app.root %>pack/<%= app.slug %>/assets/img/'
		}]
	}	

};