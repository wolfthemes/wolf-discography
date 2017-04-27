module.exports = {
	// zip the plugin folder
	build:{

		options: {
			archive: '../pack/dist/<%= app.slug %>.zip',
			mode: 'zip'
		},
		expand: true,
		cwd: '../pack/<%= app.slug %>/',
		src: [ '**/*', '!dev.config.php' ],
		dest: '<%= app.slug %>/'
	}
};