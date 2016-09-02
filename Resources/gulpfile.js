var gulp = require('gulp');
var babel = require('gulp-babel');
var gulpif = require('gulp-if');
var gutil = require('gulp-util');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
//var rewriteCSS = require('gulp-rewrite-css');
var browserify = require('browserify');
var babelify = require('babelify');
var vueify = require('vueify');
var source = require('vinyl-source-stream');

gulp.task('js', function () {
    /*return gulp.src([
     
    ])
        //.pipe(babel())
        .pipe(gulpif(gutil.env.env == 'prod',uglify()))
        .pipe(concat('app_head.js'))
        .pipe(gulp.dest('web/compiled/js'));*/
});

gulp.task('js:vue', function () {
	return browserify('./assets/js/app.js')
	.transform(babelify, { presets: ['es2015'], plugins: ["transform-runtime"] })
	.transform(vueify)
	.bundle().on("error", function(err){ gutil.log(err); this.emit('end'); })
	.pipe(source("translations.js"))
	.pipe(gulp.dest("./public/js/"));
});

gulp.task('css', function () {
    /*var dest = './web/compiled/css';

    return gulp.src([
       
    ])
        .pipe(concat('styles.css'))
        .pipe(gulpif(gutil.env.env == 'prod',uglifycss()))
        .pipe(gulp.dest(dest));*/
});

gulp.task('watch', function(){
    //gulp.watch(['**/*.{css,scss}'], { cwd: './app/Resources/assets/'}, ['css', 'admin:css']);
    //gulp.watch(['**/*.{css,scss}'], { cwd: './web/css/'}, ['css', 'admin:css']);
    gulp.watch([
        'js/app.js',
        'js/**/*.js',
        'vue/**/*.vue',
        'vue/**/*.js'
    ], { cwd: './assets'}, ['js:vue']);
});

gulp.task('default', ['css', 'js', 'js:vue']);

