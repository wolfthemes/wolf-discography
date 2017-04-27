module.exports = {
	// notify cross-OS - see https://github.com/dylang/grunt-notify
	scss: {
		options: {
			title: '<%= app.name %> SCSS compiled',
			message: 'All good with CSS'
		}
	},

	js:{
		options: {
			title: '<%= app.name %> JS',
			message: 'All good with javascript'
		}

	},

	build: {
		options: {
			title: '<%= app.name %> Plugin folder created',
			message: 'All good'
		}
	},

	dist: {
		options: {
			title: '<%= app.name %> dist',
			message: 'Plugin ready for production'
		}
	},

	stage: {
		options: {
			title: '<%= app.name %> Stage',
			message: 'Plugin uploaded on staging server'
		}
	},

	prod: {
		options: {
			title: '<%= app.name %> Production',
			message: 'Plugin uploaded on production servers'
		}
	},

	changelog: {
		options: {
			title: '<%= app.name %> Changelog updated',
			message: 'Changelog uploaded on the server'
		}
	},

	share: {
		options: {
			title: '<%= app.name %> Dropboxed and Backed up',
			message: 'Folder created'
		}
	},

	doc: {
		options: {
			title: 'Doc online',
			message: 'Doc uploaded on docs.wolfthemes.com'
		}
	},

	wolf: {
		options: {
			title: '<%= app.name %> on wolfthemes.com',
			message: 'Plugin uploaded on wolfthemes.com'
		}
	},

	help: {
		options: {
			title: '<%= app.name %> on helpdesk',
			message: 'Theme uploaded on helpdesk'
		}
	},
};