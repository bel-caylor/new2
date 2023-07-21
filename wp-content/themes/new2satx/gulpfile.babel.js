/**
 * Gulpfile.
 *
 * Gulp with WordPress.
 *
 * Implements:
 *      1. Live reloads browser with BrowserSync.
 *      3. JS: Concatenates & uglifies Vendor and Custom JS files.
 *      4. Images: Minifies PNG, JPEG, GIF and SVG images.
 *      5. Watches files for changes in JS.
 *      6. Watches files for changes in PHP.
 *      7. Corrects the line endings.
 *
 * @tutorial https://github.com/ahmadawais/WPGulp
 * @author Ahmad Awais <https://twitter.com/MrAhmadAwais/>
 */

/**
 * Load WPGulp Configuration.
 */
const config = require("./wpgulp.config.js");

/**
 * Load Plugins.
 *
 * Load gulp plugins and passing them semantic names.
 */
const gulp = require("gulp"); // Gulp of-course.

// CSS related plugins.
const wait = require("gulp-wait"); // Insert a delay before calling the next function.

// JS related plugins.
const concat = require("gulp-concat"); // Concatenates JS files.
const uglify = require("gulp-uglify"); // Minifies JS files.
const babel = require("gulp-babel"); // Compiles ESNext to browser compatible JS.

// Image related plugins.
const imagemin = require("gulp-imagemin"); // Minify PNG, JPEG, GIF and SVG images with imagemin.

// Utility related plugins.
const rename = require("gulp-rename"); // Renames files E.g. style.css -> style.min.css.
const lineec = require("gulp-line-ending-corrector"); // Consistent Line Endings for non UNIX systems. Gulp Plugin for Line Ending Corrector (A utility that makes sure your files have consistent line endings).
const notify = require("gulp-notify"); // Sends message notification to you.
const browserSync = require("browser-sync").create(); // Reloads browser and injects CSS. Time-saving synchronized browser testing.
const cache = require("gulp-cache"); // Cache files in stream for later use.
const remember = require("gulp-remember"); //  Adds all the files it has ever seen back into the stream.
const plumber = require("gulp-plumber"); // Prevent pipe breaking caused by errors from gulp plugins.
const beep = require("beepbeep");

/**
 * Custom Error Handler.
 *
 * @param Mixed err
 */
const errorHandler = (r) => {
	notify.onError("\n\n❌  ===> ERROR: <%= error.message %>\n")(r);
	beep();

	// this.emit('end');
};

/**
 * Task: `browser-sync`.
 *
 * Live Reloads, CSS injections, Localhost tunneling.
 * @link http://www.browsersync.io/docs/options/
 *
 * @param {Mixed} done Done.
 */
const browsersync = (done) => {
	browserSync.init({
		proxy: config.projectURL,
		open: config.browserAutoOpen,
		injectChanges: config.injectChanges,
		watchEvents: ["change", "add", "unlink", "addDir", "unlinkDir"],
	});
	done();
};

// Helper function to allow browser reload with Gulp 4.
const reload = (done) => {
	browserSync.reload();
	done();
};

/**
 * Task: `styles`.
 *
 * Compiles Sass, Autoprefixes it and Minifies CSS.
 *
 * This task does the following:
 *    1. Gets the source scss file
 *    2. Injects CSS or reloads the browser via browserSync
 */
gulp.task("styles", () => {
	return gulp
		.src(config.styleSRC, { allowEmpty: true })
		.pipe(wait(3000))
		.pipe(browserSync.stream()) // Reloads style.min.css if that is enqueued.
		.pipe(
			notify({ message: "\n\n✅  ===> STYLES — completed!\n", onLast: true })
		);
});

/**
 * Task: `vendorsJS`.
 *
 * Concatenate and uglify vendor JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS vendor files
 *     2. Concatenates all the files and generates vendors.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates vendors.min.js
 */
gulp.task("vendorsJS", () => {
	return gulp
		.src(config.jsVendorSRC, { since: gulp.lastRun("vendorsJS") }) // Only run on changed files.
		.pipe(plumber(errorHandler))
		.pipe(
			babel({
				presets: [
					[
						"@babel/preset-env", // Preset to compile your modern JS to ES5.
						{
							targets: { browsers: config.BROWSERS_LIST }, // Target browser list to support.
						},
					],
				],
			})
		)
		.pipe(remember(config.jsVendorSRC)) // Bring all files back to stream.
		.pipe(concat(config.jsVendorFile + ".js"))
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(gulp.dest(config.jsVendorDestination))
		.pipe(
			rename({
				basename: config.jsVendorFile,
				suffix: ".min",
			})
		)
		.pipe(uglify())
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(gulp.dest(config.jsVendorDestination))
		.pipe(
			notify({ message: "\n\n✅  ===> VENDOR JS — completed!\n", onLast: true })
		);
});

/**
 * Task: `editorJS`.
 *
 * Concatenate and uglify editor JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS editor files
 *     2. Concatenates all the files and generates editor.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates editor.min.js
 */
gulp.task("editorJS", () => {
	return gulp
		.src(config.jsEditorSRC, { since: gulp.lastRun("editorJS") }) // Only run on changed files.
		.pipe(plumber(errorHandler))
		.pipe(
			babel({
				presets: [
					[
						"@babel/preset-env", // Preset to compile your modern JS to ES5.
						{
							targets: { browsers: config.BROWSERS_LIST }, // Target browser list to support.
						},
					],
				],
			})
		)
		.pipe(remember(config.jsEditorSRC)) // Bring all files back to stream.
		.pipe(concat(config.jsEditorFile + ".js"))
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(gulp.dest(config.jsEditorDestination))
		.pipe(
			rename({
				basename: config.jsEditorFile,
				suffix: ".min",
			})
		)
		.pipe(uglify())
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(gulp.dest(config.jsEditorDestination))
		.pipe(
			notify({ message: "\n\n✅  ===> EDITOR JS — completed!\n", onLast: true })
		);
});

/**
 * Task: `customJS`.
 *
 * Concatenate and uglify custom JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS custom files
 *     2. Concatenates all the files and generates custom.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates custom.min.js
 */
gulp.task("customJS", () => {
	return gulp
		.src(config.jsCustomSRC, { since: gulp.lastRun("customJS") }) // Only run on changed files.
		.pipe(plumber(errorHandler))
		.pipe(
			babel({
				presets: [
					[
						"@babel/preset-env", // Preset to compile your modern JS to ES5.
						{
							targets: { browsers: config.BROWSERS_LIST }, // Target browser list to support.
						},
					],
				],
			})
		)
		.pipe(remember(config.jsCustomSRC)) // Bring all files back to stream.
		.pipe(concat(config.jsCustomFile + ".js"))
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(gulp.dest(config.jsCustomDestination))
		.pipe(
			rename({
				basename: config.jsCustomFile,
				suffix: ".min",
			})
		)
		.pipe(uglify())
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(gulp.dest(config.jsCustomDestination))
		.pipe(
			notify({ message: "\n\n✅  ===> CUSTOM JS — completed!\n", onLast: true })
		);
});

/**
 * Task: `images`.
 *
 * Minifies PNG, JPEG, GIF and SVG images.
 *
 * This task does the following:
 *     1. Gets the source of images raw folder
 *     2. Minifies PNG, JPEG, GIF and SVG images
 *     3. Generates and saves the optimized images
 *
 * This task will run only once, if you want to run it
 * again, do it with the command `gulp images`.
 *
 * Read the following to change these options.
 * @link https://github.com/sindresorhus/gulp-imagemin
 */
gulp.task("images", () => {
	return gulp
		.src(config.imgSRC)
		.pipe(
			cache(
				imagemin([
					imagemin.gifsicle({ interlaced: true }),
					imagemin.jpegtran({ progressive: true }),
					imagemin.optipng({ optimizationLevel: 3 }), // 0-7 low-high.
					imagemin.svgo({
						plugins: [{ removeViewBox: false }, { cleanupIDs: false }],
					}),
				])
			)
		)
		.pipe(gulp.dest(config.imgDST))
		.pipe(
			notify({ message: "\n\n✅  ===> IMAGES — completed!\n", onLast: true })
		);
});

/**
 * Task: `clear-images-cache`.
 *
 * Deletes the images cache. By running the next "images" task,
 * each image will be regenerated.
 */
gulp.task("clearCache", function (done) {
	return cache.clearAll(done);
});

/**
 * Watch Tasks.
 *
 * Watches for file changes and runs specific tasks.
 */
gulp.task(
	"default",
	gulp.parallel(
		"styles",
		"vendorsJS",
		"editorJS",
		"customJS",
		"images",
		browsersync,
		() => {
			gulp.watch(config.watchPhp, gulp.series(reload)); // Reload on PHP file changes.
			gulp.watch(config.watchStyles, gulp.parallel("styles")); // Reload on SCSS file changes.
			gulp.watch(config.watchJsVendor, gulp.series("vendorsJS", reload)); // Reload on vendorsJS file changes.
			gulp.watch(config.watchJsEditor, gulp.series("editorJS", reload)); // Reload on editorJS file changes.
			gulp.watch(config.watchJsCustom, gulp.series("customJS", reload)); // Reload on customJS file changes.
			gulp.watch(config.imgSRC, gulp.series("images", reload)); // Reload on customJS file changes.
		}
	)
);
