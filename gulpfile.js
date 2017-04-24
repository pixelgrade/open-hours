var plugin = 'open',
	source_SCSS = { public: './includes/scss/**/*.scss'},
	dest_CSS = { public: './includes/css/'},

	gulp 		= require('gulp'),
	sass 		= require('gulp-sass'),
	prefix 		= require('gulp-autoprefixer'),
	exec 		= require('gulp-exec'),
	del         = require('del');

require('es6-promise').polyfill();

gulp.task('styles', function () {
	return gulp.src(source_SCSS.public)
		.pipe(sass({'sourcemap': false, style: 'compact'}))
		.on('error', function (e) {
			console.log(e.message);
		})
		.pipe(prefix("last 1 version", "> 1%", "ie 8", "ie 7"))
		.pipe(gulp.dest(dest_CSS.public));
});

/**
 * Create a zip archive out of the cleaned folder and delete the folder
 */
gulp.task( 'zip', ['build'], function() {
	return gulp.src( './' )
		.pipe( exec( 'cd ./../; rm -rf Open-Hours-1-0-0.zip; cd ./build/; zip -r -X ./../Open-Hours-1-0-0.zip ./open-hours; cd ./../; rm -rf build' ) );

} );

/**
 * Copy theme folder outside in a build folder, recreate styles before that
 */
gulp.task( 'copy-folder', function() {
	return gulp.src( './' )
		.pipe( exec( 'rm -Rf ./../build; mkdir -p ./../build/open-hours; cp -Rf ./* ./../build/open-hours/' ) );
} );

/**
 * Clean the folder of unneeded files and folders
 */
gulp.task( 'build', ['copy-folder'], function() {

	// files that should not be present in build zip
	var files_to_remove = [
		'**/codekit-config.json',
		'node_modules',
		'tests',
		'.travis.yml',
		'.babelrc',
		'.gitignore',
		'circle.yml',
		'phpunit.xml.dist',
		'.sass-cache',
		'config.rb',
		'gulpfile.js',
		'package.json',
		'pxg.json',
		'build',
		'.idea',
		'**/*.css.map',
		'**/.git*',
		'*.sublime-project',
		'.DS_Store',
		'**/.DS_Store',
		'__MACOSX',
		'**/__MACOSX',
		'+development.rb',
		'+production.rb',
		'README.md',
		'admin/src',
		'admin/scss',
		'admin/js/*.map',
		'admin/css/*.map',
		'.labels'
	];

	files_to_remove.forEach( function( e, k ) {
		files_to_remove[k] = '../build/open-hours/' + e;
	} );

	del.sync(files_to_remove, {force: true});
} );

var sourcemaps = require('gulp-sourcemaps');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var browserify = require('browserify');
var watchify = require('watchify');
var react = require('react');
var reactdom = require('react-dom');
var babel = require('babelify');

gulp.task('react', function() { return compile(); });
