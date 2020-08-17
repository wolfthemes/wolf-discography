module.exports = {
	options:{

		noLineComments : true
	},

	build:{
		options:{
			sassDir: '<%= app.scssPath %>',
			cssDir: '<%= app.cssPath %>',
			imagesDir : '<%= app.imagesPath %>'
		}
	},

	admin:{
		options:{
			sassDir: '<%= app.root %>/scss-admin',
			cssDir: '<%= app.cssPath %>/admin/'
		}
	},
};