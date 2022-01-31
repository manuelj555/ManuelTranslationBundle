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
var buffer = require('vinyl-buffer');
var sourcemaps = require('gulp-sourcemaps');
var watchify = require('watchify');

var config = {
    isProd: gutil.env.env == 'prod',
    resourcesPath: './assets/',
    targetPath: './public/',
    jsTarget: './public/js/',
    cssTarget: './public/css/',
};

gulp.task('js', function () {
    /*return gulp.src([
     
    ])
        //.pipe(babel())
        .pipe(gulpif(gutil.env.env == 'prod',uglify()))
        .pipe(concat('app_head.js'))
        .pipe(gulp.dest('web/compiled/js'));*/
});

gulp.task('js:vue', function () {

    var bundler = browserify({
        entries: config.resourcesPath + "js/app.js",
        paths: [
            './node_modules',
            config.resourcesPath + 'js',
            config.resourcesPath + 'vue',
        ],
        debug: true,
        cache: {},
        packageCache: {},
    }).transform(babelify, {
        presets: ['es2015'], plugins: ["transform-runtime"],
        sourceMaps: true,
    }).transform(vueify);

    function rebundle() {
        return bundler.bundle().on('error', function (err) {
            err.stream = null; // Quitamos contenido no leible
            console.log('     #########  ERROR   ###########');
            console.log(err);
            this.emit('end');
        })
            .pipe(source("translations.js"))
            .pipe(buffer())
            .pipe(gulpif(!config.isProd, sourcemaps.init({loadMaps: true})))
            .pipe(gulpif(config.isProd, uglify()))
            .pipe(gulpif(!config.isProd, sourcemaps.write('.')))
            .pipe(gulp.dest(config.jsTarget))
    }

    if (gutil.env.watch) {
        bundler.plugin(watchify);

        bundler.on('update', function () {
            rebundle();
            gutil.log('Compilando Archivos de Vue...');
        }).on('time', function (time) {
            gutil.log('Listo en: ', gutil.colors.cyan(time + ' ms'));
        });
    }


    return rebundle();
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

