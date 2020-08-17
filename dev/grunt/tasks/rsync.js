module.exports = {

	options: {
		args: ["--verbose", "--chmod=Du=rwx,Dg=rx,Do=rx,Fu=rw,Fg=r,Fo=r"],
		//args: ["--verbose", "ssh 'C:/Program Files/Git/usr/bin/ssh.exe -p 18765'"],
		//args: ["--verbose"],
		recursive: true
	},

	// sandbox
	sandbox: {
		options: {
			src: [ '<%= app.root %>/pack/<%= app.slug %>/' ],
			dest: "/home/csag/http/wolfthemes.com/sandbox/wp-content/plugins/<%= app.slug %>",
			host: "wolf",
			syncDestIgnoreExcl: true
		}
	},

	// stage
	stage: {
		options: {
			src: [ '<%= app.root %>/pack/<%= app.slug %>/' ],
			dest: "/home/csag/http/wolfthemes.com/stage/wp-content/plugins/<%= app.slug %>",
			host: "wolf",
			syncDestIgnoreExcl: true
		}
	},

	newold: {
		options: {
			src: [ '<%= app.root %>/pack/<%= app.slug %>/' ],
			dest: "/home/customer/www/demo.constantins2.sg-host.com/public_html/wp-content/plugins/<%= app.slug %>",
			host: "sg", // set in ~/.ssh config
			syncDestIgnoreExcl: true,
			port: '18765'
 		}
	},

	// demo
	olddemo: {
		options: {
			src: [ '<%= app.root %>/pack/<%= app.slug %>/' ],
			dest: "/home/csag/http/wolfthemes.com/demos/wp-content/plugins/<%= app.slug %>",
			host: "wolf",
			syncDestIgnoreExcl: true
		}
	},

	demo: {
		options: {
			src: [ '<%= app.root %>/pack/<%= app.slug %>/' ],
			dest: "/home/customer/www/demos.constantins2.sg-host.com/public_html/wp-content/plugins/<%= app.slug %>",
			host: "sg", // set in ~/.ssh config
			syncDestIgnoreExcl: true,
			port: '18765'
 		}
	},

	envato: {
		options: {
			src: [ '<%= app.root %>/pack/<%= app.slug %>/' ],
			dest: "/home/customer/www/envato.constantins2.sg-host.com/public_html/wp-content/plugins/<%= app.slug %>",
			host: "sg", // set in ~/.ssh config
			syncDestIgnoreExcl: true,
			port: '18765'
 		}
	},

	// wolf
	wolf: {
		options: {
			src: [ '<%= app.root %>/pack/<%= app.slug %>/' ],
			dest: "/home/csag/http/wolfthemes.com/www/wp-content/plugins/<%= app.slug %>",
			host: "wolf",
			syncDestIgnoreExcl: true
		}
	},

	// boss
	boss: {
		options: {
			src: [ '<%= app.root %>/pack/<%= app.slug %>/' ],
			dest: "/home/csag/http/billbossrider.com/www/wp-content/plugins/<%= app.slug %>",
			host: "wolf",
			syncDestIgnoreExcl: true
		}
	},

	doc:{
		options: {
			src: [ '<%= app.root %>/pack/Documentation/' ],
			dest: "/home/csag/http/wolfthemes.com/docs/documentation/plugins/<%= app.slug %>",
			host: "wolf",
			syncDestIgnoreExcl: true
		}
	},

	// new wolfthemes
	dist: {
		options: {
			src: [ '<%= app.root %>/pack/dist/' ],
			dest: "/home/csag/http/wolfthemes.com/plugins/<%= app.slug %>",
			host: "wolf",
			syncDestIgnoreExcl: true
		}
	},

	newdist: {
		options: {
			src: [ '<%= app.root %>/pack/dist/' ],
			dest: "/home/customer/www/plugins.constantins2.sg-host.com/public_html/<%= app.slug %>",
			host: "sg", // set in ~/.ssh config
			syncDestIgnoreExcl: true,
			port: '18765'
		}
	}
};