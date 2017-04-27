module.exports = {
	log : {
		files: [
			{
				expand: true,
				cwd : '../',
				src: 'changelog.md',
				dest: '../pack/dist/',
				ext: '.xml'
			}
		],
		options: {
			template: 'log-template.jst',
			templateContext: {
				version: '<%= version %>'
			}
		}
	}
};