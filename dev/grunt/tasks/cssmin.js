module.exports = {
	
	main : {
		options: {
			// noAdvanced: true,
			// compatibility : true,
			// debug : true
			// keepBreaks : true
		},
		files: {
			'<%= app.cssPath %>/discography.min.css': [
				'<%= app.cssPath %>/discography.css'
			]
		}
	}
};