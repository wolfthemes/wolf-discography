module.exports = {
	target: {
            options: {
		cwd: '<%= app.root %>/pack/<%= app.slug %>/',                          // Directory of files to internationalize.
		domainPath: 'languages',                   // Where to save the POT file.
		exclude: [],
		include: [],
		mainFile: '<%= app.slug %>.php', // Main project file.
		potComments: '', // The copyright at the beginning of the POT file.
		potFilename: '', // Name of the POT file.
		potHeaders: {
				poedit: true          // Includes common Poedit headers.
			},                                // Headers to add to the generated POT file.
			processPot: null,                 // A callback function for manipulating the POT file.
			type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
			updateTimestamp: true,             // Whether the POT-Creation-Date should be updated without other changes.
			updatePoFiles: false              // Whether to update PO files in the same directory as the POT file.
		}
        }
};