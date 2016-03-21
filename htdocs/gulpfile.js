'use strict';

/**
 * Source Directories
 *
 * @type {string}
 */
let srcDir = './resources/assets';
let jsSrcDir = srcDir + '/js';
let jsTestsSrcDir = 'tests/js';
let distDir = 'public';
let cssDist = distDir + '/css';
let jsDist = distDir + '/js';



/**
 * Module exports
 *
 * @type {...|exports|module.exports}
 */
require('node-jsx').install({extension: '.jsx'});
let browserify = require('browserify');
let reactify = require('reactify');
let jshint = require('gulp-jshint');
let mocha = require('gulp-mocha');
let gulp = require('gulp');
let babel = require('gulp-babel');
let babelRegister = require('babel-register');
let source = require('vinyl-source-stream');
let buffer = require('vinyl-buffer');
let uglify = require('gulp-uglify');
let sourcemaps = require('gulp-sourcemaps');
let gutil = require('gulp-util');

let elixir = require('laravel-elixir');
let phplint = require('laravel-elixir-phplint');



/**
 * Custom JavaScript Gulp Task
 *
 * NOTE: elixir's browserify wrapper doesn't support sourcemap generation by default yet
 */
gulp.task('javascript', () => {
    // set up the browserify instance on a task basis
    let b = browserify({
        entries: jsSrcDir + '/app.js',
        debug: true,
        transform: [reactify]

    });

    return b.bundle()
        .pipe(source('app.js'))
        .pipe(buffer())
        .pipe(sourcemaps.init({loadMaps: true}))
        // Add transformation tasks to the pipeline here.
        .pipe(babel()) //TODO babel() appears to take a lot of time, optimise.... optimise...
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(jsDist));
});

/**
 * JS lint task for verifying javascript code quality.
 *
 * returns: gulp stream
 */
gulp.task('jslint', () => {
    return gulp.src([jsSrcDir+'**/*.js', jsSrcDir+'**/*.jsx', jsTestsSrcDir+'/**/*.spec.js'] )
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

/**
 * Mocha Test wrapper method to DRY the code a bit.
 *
 * returns: gulp stream
 *
 * TODO: Refactore it see mocha-* fulp tasks
 */
function mochaTest(dir , rep) {
    let _dir = dir || '/';
    let _rep = rep || 'spec';
    let __dir = jsTestsSrcDir+_dir+'/**/*.spec.js';

    return gulp.src(__dir, {read: false})
        // gulp-mocha needs filepaths so you can't have any plugins before it
        .pipe(mocha({
            reporter: _rep,
            compilers: {
                js: babelRegister
            }}));
}

/**
 * It loads files from tests/js/api & runs js api tests with mocha & chakram
 */
gulp.task('mocha-api' , () => {
    return mochaTest('/api', 'nyan');
});

/**
 * It loads files from tests/js/unit & runs js api tests with mocha
 */
gulp.task('mocha-unit' , () => {
    return mochaTest('/unit', 'nyan');
});


gulp.task('tdd-js', function() {
    gulp.run(['jslint', 'mocha-unit']);
    gulp.watch([jsSrcDir+'/**/*js', jsSrcDir+'/**/*jsx'], ['jslint', 'mocha-unit']);
    gulp.watch(jsTestsSrcDir+'/**/*js', ['jslint','mocha-unit']);
});


/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir((mix) => {

    //mix.phplint([   //TODO enable & configure phpUnit tests properly.
    //    'app/**/*.php',
    //    'test/**/*.php'
    //]);
    //mix.phpSpec();
    //mix.phpUnit();

    //Javascript
    mix.task('jslint');
    mix.task('mocha-unit');
    //mix.task('mocha-api'); //No api tests at the moment, enable when we have some
    mix.task('javascript');

    //Styles
    mix.less('app.less', cssDist);
    mix.version([cssDist + '/app.css', jsDist + '/app.js']);

});
