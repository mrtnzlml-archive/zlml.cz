var path = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var webpack = require('webpack');
var ManifestPlugin = require('webpack-manifest-plugin');
var autoprefixer = require('autoprefixer');

var paths = {
	appIndexJs: './www/src/js/main.js',
	appIndexCss: './www/src/css/screen.css',
	appSrc: './www/src',
	appBuild: './www/dist',
	nodePaths: path.resolve(__dirname, 'node_modules'),
};
var publicPath = '/dist/';

module.exports = {
	// Don't attempt to continue if there are any errors.
	bail: true,
	// We generate sourcemaps in production. This is slow but gives good results.
	// You can exclude the *.map files from the build during deployment.
	devtool: 'source-map', //TODO: 'eval' for development,
	// In production, we only want to load the app code.
	entry: {
		'main': [
			paths.appIndexJs,
			paths.appIndexCss,
			paths.appSrc + '/css/fshl.css',
		],
		'admin': [
			'./www/src/js/admin.js',
			'./www/src/css/admin.css',
			paths.nodePaths + '/nette.ajax.js/nette.ajax.js'
		]
	},
	output: {
		// The build folder.
		path: paths.appBuild,
		// Add /* filename */ comments to generated require()s in the output.
		//pathinfo: true, //FIXME: only dev
		// Generated JS file names (with nested folders).
		// There will be one main bundle, and one file per asynchronous chunk.
		// We don't currently advertise code splitting but Webpack supports it.
		filename: 'js/[name].[chunkhash:8].js',
		chunkFilename: 'js/[name].[chunkhash:8].chunk.js',
		// We inferred the "public path" (such as / or /my-project) from homepage.
		publicPath: publicPath
	},
	resolve: {
		// This allows you to set a fallback for where Webpack should look for modules.
		// We read `NODE_PATH` environment variable in `paths.js` and pass paths here.
		// We use `fallback` instead of `root` because we want `node_modules` to "win"
		// if there any conflicts. This matches Node resolution mechanism.
		// https://github.com/facebookincubator/create-react-app/issues/253
		fallback: paths.nodePaths,
		// These are the reasonable defaults supported by the Node ecosystem.
		// We also include JSX as a common component filename extension to support
		// some tools, although we do not recommend using it, see:
		// https://github.com/facebookincubator/create-react-app/issues/290
		extensions: ['.js', '.json', '.jsx', '.css', ''],
		alias: {
			// Support React Native Web
			// https://www.smashingmagazine.com/2016/08/a-glimpse-into-the-future-with-react-native-for-web/
			'react-native': 'react-native-web'
		}
	},
	module: {
		// First, run the linter.
		// It's important to do this before Babel processes the JS.
		preLoaders: [
			{
				test: /\.(js|jsx)$/,
				loader: 'eslint',
				// include: paths.appSrc
			}
		],
		loaders: [
			// Process JS with Babel.
			{
				test: /\.(js|jsx)$/,
				// include: paths.appSrc,
				loader: 'babel',
				exclude: /node_modules/,
				query: {
					presets: ['es2015', 'stage-0']
				}
			},
			// The notation here is somewhat confusing.
			// "postcss" loader applies autoprefixer to our CSS.
			// "css" loader resolves paths in CSS and adds assets as dependencies.
			// "style" loader normally turns CSS into JS modules injecting <style>,
			// but unlike in development configuration, we do something different.
			// `ExtractTextPlugin` first applies the "postcss" and "css" loaders
			// (second argument), then grabs the result CSS and puts it into a
			// separate file in our build process. This way we actually ship
			// a single CSS file in production instead of JS code injecting <style>
			// tags. If you use code splitting, however, any async bundles will still
			// use the "style" loader inside the async code so CSS from them won't be
			// in the main CSS file.
			{
				test: /\.css$/,
				// "?-autoprefixer" disables autoprefixer in css-loader itself:
				// https://github.com/webpack/css-loader/issues/281
				// We already have it thanks to postcss. We only pass this flag in
				// production because "css" loader only enables autoprefixer-powered
				// removal of unnecessary prefixes when Uglify plugin is enabled.
				// Webpack 1.x uses Uglify plugin as a signal to minify *all* the assets
				// including CSS. This is confusing and will be removed in Webpack 2:
				// https://github.com/webpack/webpack/issues/283
				loader: ExtractTextPlugin.extract('style', 'css?importLoaders=1&-autoprefixer!postcss')
				// Note: this won't work without `new ExtractTextPlugin()` in `plugins`.
			},
			// JSON is not enabled by default in Webpack but both Node and Browserify
			// allow it implicitly so we also enable it.
			{
				test: /\.json$/,
				loader: 'json'
			},
			// "file" loader makes sure those assets end up in the `build` folder.
			// When you `import` an asset, you get its filename.
			{
				test: /\.(ico|jpg|jpeg|png|gif|eot|otf|webp|svg|ttf|woff|woff2)(\?.*)?$/,
				loader: 'file',
				query: {
					name: 'media/[name].[hash:8].[ext]'
				}
			},
			// "url" loader works just like "file" loader but it also embeds
			// assets smaller than specified size as data URLs to avoid requests.
			{
				test: /\.(mp4|webm|wav|mp3|m4a|aac|oga)(\?.*)?$/,
				loader: 'url',
				query: {
					limit: 10000,
					name: 'media/[name].[hash:8].[ext]'
				}
			}
		]
	},
	// We use PostCSS for autoprefixing only.
	postcss: function () {
		return [
			autoprefixer({
				browsers: [
					'>1%',
					'last 4 versions',
					'Firefox ESR',
					'not ie < 9', // React doesn't support IE8 anyway
				]
			}),
		];
	},
	plugins: [
		// This helps ensure the builds are consistent if source hasn't changed:
		new webpack.optimize.OccurrenceOrderPlugin(),
		// Try to dedupe duplicated modules, if any:
		new webpack.optimize.DedupePlugin(),
		// Minify the code.
		new webpack.optimize.UglifyJsPlugin({
			compress: {
				screw_ie8: true, // React doesn't support IE8
				warnings: false
			},
			mangle: {
				screw_ie8: true
			},
			output: {
				comments: false,
				screw_ie8: true
			}
		}),
		// Note: this won't work without ExtractTextPlugin.extract(..) in `loaders`.
		new ExtractTextPlugin('css/[name].[contenthash:8].css'),
		// Generate a manifest file which contains a mapping of all asset filenames
		// to their corresponding output file so that tools can pick it up without
		// having to parse `index.html`.
		new ManifestPlugin({
			fileName: 'asset-manifest.json'
		}),
		new webpack.ProvidePlugin({
			'window.jQuery': 'jquery',
			$: "jquery",
			jQuery: "jquery"
		}),
		function () {
			this.plugin("done", function (stats) {
				require("fs").writeFileSync(
					path.join(__dirname, 'www', 'dist', 'webpack-stats.json'),
					JSON.stringify(stats.toJson()));
			});
		}
	],
	// Some libraries import Node modules but don't use them in the browser.
	// Tell Webpack to provide empty mocks for them so importing them works.
	node: {
		fs: 'empty',
		net: 'empty',
		tls: 'empty'
	}
};
