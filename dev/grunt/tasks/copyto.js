module.exports = {
	// create a clean theme folder without the junk
	build: {
		files: [
			{ cwd: '<%= app.root %>/', src: [ '**/*' ], dest: '<%= app.root %>/pack/<%= app.slug %>/' }
		],
		options: {
			ignore: '<%= app.ignoreFiles %>'
		}
	},

	test : {
		files: [
			{ cwd: '<%= app.root %>/pack/<%= app.slug %>', src: [ '**/*' ], dest: '<%= app.testPath %>/wp-content/plugins/<%= app.slug %>/' }
		]
	}
};