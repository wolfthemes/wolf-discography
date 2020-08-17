module.exports = {
	build: {
		src: ['<%= app.root %>/pack/<%= app.slug %>'],
		options: {
			force: true
		}
	}
};