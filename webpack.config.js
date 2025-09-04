require('dotenv').config();
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const path = require('path');

module.exports = () => {
	// npm run build-theme --theme=clix

	const plugin = "wolf-discography"

    if (!plugin) {
        console.error('Error: No plugin specified.');
        process.exit(1);
    }

    console.log(`Building plugin: ${plugin}`);

	return {
	    ...defaultConfig,
		// Add webpack dev server and watch options for WSL
		watchOptions: {
			poll: 1000, // Check for changes every second
			aggregateTimeout: 300, // Delay before rebuilding
			ignored: /node_modules/,
		},
		module: {
			...defaultConfig.module,
			rules: [
				...defaultConfig.module.rules,
				// Add TypeScript support
				{
					test: /\.tsx?$/,
					use: [
						{
							loader: 'ts-loader',
							options: {
								configFile: path.resolve(__dirname, 'tsconfig.json'),
								// Allow .js files to import .ts files
								allowTsInNodeModules: true,
								transpileOnly: true, // Faster builds, type checking handled separately
							},
						},
					],
					exclude: /node_modules/,
				},
				// Add shader file support (like vite-plugin-glsl)
				{
					test: /\.(glsl|vs|fs|vert|frag)$/,
					use: 'raw-loader'
				},
			],
		},
		plugins: [
			...defaultConfig.plugins,
			new BrowserSyncPlugin(
				{
				files: [
					'**/*.php',
					'**/*.js',
					'**/*.ts', // Add TypeScript files to watch
					'**/*.tsx', // Add TypeScript React files to watch
					'**/*.scss',
					'**/*.css',
					`./build/**/*` // Watch build directory
				],
				host: 'localhost',
				port: 3000,
				proxy: 'http://wolfthemes.local/',
				watchOptions: {
					poll: 1000,
					ignoreInitial: true,
					usePolling: true, // Force polling
					interval: 1000,
					binaryInterval: 1000
				},
				notify: true,
				callbacks: {
					files: {
						match: function(event, file) {
							console.log('File changed:', file);
							return true;
						}
					}
				}
			},
			{
				reload: true,
				injectChanges: false // Force full reload instead of injecting changes
			}),
		],
		entry: {
			app : {
				import: [
					// Change this to .ts when you rename plugin.js to theme.ts
					path.resolve( __dirname, './src/scripts/plugin.js' )
				],
				filename: './app.js'
			},
			// admin: {
			// 	import: path.resolve( __dirname, './src/admin/elementor-admin.js' ),
			// 	filename: './elementor-admin.js'
			// },
			styles : {
				import: [
					path.resolve( __dirname, './src/styles/main.scss' )
				],
			},
			// editor : {
			// 	import: [
			// 		path.resolve( __dirname, './src/styles/admin.scss' )
			// 	],
			// },
		},
		output: {
			clean: true,
			path: path.resolve(__dirname, 'build' ),
			publicPath: './',
		},
		resolve: {
			alias: {
				'@themeSrc': path.resolve(__dirname, './src'),
				'@utils': path.resolve(__dirname, './my-modules/utils'),
				'@shared': path.resolve(__dirname, './my-modules/shared'),
			},
			modules: ['node_modules'],
			mainFiles: ['index', 'main', 'theme'],
			extensions: ['.js', '.json', '.jsx', '.ts', '.tsx'], // You already have this, perfect!
		}
	}
};