/**
 * Replace string multiple times
 */
module.exports = {
	build : {
		src: [ 
			'../pack/<%= app.slug %>/**/*.php',
			'../pack/<%= app.slug %>/assets/js/*.js',
			'../pack/<%= app.slug %>/assets/js/min/*.js'
		],
    		overwrite: true,
		replacements: [
			{
				from: '%VERSION%',
				to: '<%= version %>'
			},
			{
				from: '%NAME%',
				to: '<%= app.name %>'
			},
			{
				from: '%PACKAGENAME%',
				to: '<%= app.packagename %>'
			},
			{
				from: '%SLUG%',
				to: '<%= app.slug %>'
			},
			{
				from: '%TEXTDOMAIN%',
				to: '<%= app.textdomain %>'
			},
			{
				from: '%CREATED%',
				to: '<%= app.created %>'
			},
			{
				from: '%CREATEDNICE%',
				to: '<%= app.createdNice %>'
			},
			{
				from: '%REQUIRES%',
				to: '<%= app.requires %>'
			},
			{
				from: '%TESTED%',
				to: '<%= app.tested %>'
			},
			{
				from: '%DESCRIPTION%',
				to: '<%= app.description %>'
			},
			{
				from: '%DATE%',
				to: '<%= app.updated %>'
			},
			{
				from: '%LINK%',
				to: '<%= link %>'
			},
			{
				from: '%SHORTLINK%',
				to: '<%= shortlink %>'
			},
			{
				from: '%TAGS%',
				to: '<%= app.tags %>'
			},
			{
				from: '%AUTHOR%',
				to: '<%= app.author %>'
			},
			{
				from: '%AUTHORURI%',
				to: '<%= app.authorURI %>'
			}
		]
	},

	doc : {
		overwrite: false,
		src: ['../html/doc.html'], // source files array (supports minimatch)
		dest: '../pack/Documentation/index.html',
		replacements: [
			{
				from: '%VERSION%',
				to: '<%= version %>'
			},
			{
				from: '%NAME%',
				to: '<%= app.name %>'
			},
			{
				from: '%SLUG%',
				to: '<%= app.slug %>'
			},
			{
				from: '%CREATED%',
				to: '<%= app.created %>'
			},
			{
				from: '%CREATEDNICE%',
				to: '<%= app.createdNice %>'
			},
			{
				from: '%REQUIRES%',
				to: '<%= app.requires %>'
			},

			{
				from: '%TESTED%',
				to: '<%= app.tested %>'
			},
			{
				from: '%DESCRIPTION%',
				to: '<%= app.description %>'
			},
			{
				from: '%DATE%',
				to: '<%= app.updated %>'
			},
			{
				from: '%LINK%',
				to: '<%= link %>'
			},
			{
				from: '%SHORTLINK%',
				to: '<%= shortlink %>'
			},
			{
				from: '%TAGS%',
				to: '<%= app.tags %>'
			},
			{
				from: '%AUTHOR%',
				to: '<%= app.author %>'
			},
			{
				from: '%AUTHORURI%',
				to: '<%= app.authorURI %>'
			},
			{
				from: '%WARNING%',
				top: '<%= warning %>'
			},
			{
				from: '%INFO%',
				top: '<%= info %>'
			},
			{
				from: '%NEWS%',
				top: '<%= news %>'
			},
			{
				from: 'http://docs.cdn.wolfthemes.com/documentation/plugins/<%= app.slug %>/assets/',
				to: 'assets/'
			}
		]
	}
};