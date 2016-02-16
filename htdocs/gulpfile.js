'use strict';

var distFile = 'public';
var cssDist = distFile+'/css';
var jsDist = distFile+'/js';



var browserify = require('browserify');
var gulp = require('gulp');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var gutil = require('gulp-util');
var elixir = require('laravel-elixir');


/**
 * Custom JavaScript Gulp Task
 *
 * NOTE: elixir's browserify wrapper doesn't support sourcemap generation by default yet
 */


gulp.task('javascript', function () {
    // set up the browserify instance on a task basis
    var b = browserify({
        entries: './resources/assets/js/app.js',
        debug: true
    });

    return b.bundle()
        .pipe(source('app.js'))
        .pipe(buffer())
        .pipe(sourcemaps.init({loadMaps: true}))
        // Add transformation tasks to the pipeline here.
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(jsDist));
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

elixir(function(mix) {
    mix.less('app.less', cssDist);
    mix.task('javascript');
    mix.version([cssDist+'/app.css', jsDist+'/app.js']);
});
