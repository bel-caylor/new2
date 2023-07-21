/**
 * WPGulp Configuration File
 *
 * 1. Edit the variables as per your project requirements.
 * 2. In paths you can add <<glob or array of globs>>.
 *
 * @package WPGulp
 */


 /**
 * Load Local Dev Configuration.
 */
  const config = require( './localdev.config.js' );

  module.exports = {
  
	  // Project options.
	  projectURL: config.localdevURL, // Local project URL of your already running WordPress site. Could be something like wpgulp.local or localhost:3000 depending upon your local WordPress setup.
	  productURL: './', // Theme/Plugin URL. Leave it like it is, since our gulpfile.js lives in the root folder.
	  browserAutoOpen: false,
	  injectChanges: true,
  
	  // Style options.
	  styleSRC: './dist/styles/main.min.css', // Path to main .scss file.
	  styleDestination: './dist/styles/', // Path to place the compiled CSS file. Default set to root folder.
	  outputStyle: 'compact', // Available options → 'compact' or 'compressed' or 'nested' or 'expanded'
	  errLogToConsole: true,
	  precision: 10,
  
	  // Admin options.
	  cssAdminSRC: './assets/css/style-editor.css', // Path to main .scss file.
	  cssAdminDestination: './dist/styles/', // Path to place the compiled CSS file. Default set to root folder.
	  outputStyle: 'compact', // Available options → 'compact' or 'compressed' or 'nested' or 'expanded'
	  errLogToConsole: true,
	  precision: 10,
  
	  // JS Vendor options.
	  jsVendorSRC: './assets/js/vendor/*.js', // Path to JS vendor folder.
	  jsVendorDestination: './dist/scripts/', // Path to place the compiled JS vendors file.
	  jsVendorFile: 'vendor', // Compiled JS vendors file name. Default set to vendors i.e. vendors.js.
  
	  // JS Custom options.
	  jsCustomSRC: './assets/js/custom/*.js', // Path to JS custom scripts folder.
	  jsCustomDestination: './dist/scripts/', // Path to place the compiled JS custom scripts file.
	  jsCustomFile: 'main', // Compiled JS custom file name. Default set to custom i.e. custom.js.
  
	  // JS Custom options.
	  jsEditorSRC: './assets/js/editor/*.js', // Path to JS editor scripts folder.
	  jsEditorDestination: './dist/scripts/', // Path to place the compiled JS editor scripts file.
	  jsEditorFile: 'editor', // Compiled JS custom file name. Default set to editor i.e. editor.js.
  
	  // Images options.
	  imgSRC: './assets/img/**/*', // Source folder of images which should be optimized and watched. You can also specify types e.g. raw/**.{png,jpg,gif} in the glob.
	  imgDST: './dist/images/', // Destination folder of optimized images. Must be different from the imagesSRC folder.
  
	  // Watch files paths.
	  watchStyles: './assets/css/**/*.css', // Path to all *.scss files inside css folder and inside them.
	  watchJsVendor: './assets/js/vendor/*.js', // Path to all vendor JS files.
	  watchJsCustom: './assets/js/custom/*.js', // Path to all custom JS files.
	  watchJsEditor: './assets/js/editor/*.js', // Path to all custom JS files.
	  watchPhp: './**/*.php', // Path to all PHP files.
  
	  // Browsers you care about for autoprefixing. Browserlist https://github.com/ai/browserslist
	  // The following list is set as per WordPress requirements. Though, Feel free to change.
	  BROWSERS_LIST: [
		  'last 2 versions',
		  '> 1%',
		  'ie >= 11',
		  'last 1 Android versions',
		  'last 1 ChromeAndroid versions',
		  'last 2 Chrome versions',
		  'last 2 Firefox versions',
		  'last 2 Safari versions',
		  'last 2 iOS versions',
		  'last 2 Edge versions',
		  'last 2 Opera versions'
	  ]
  };
  