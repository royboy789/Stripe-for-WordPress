var gulp = require('gulp'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat');

var jsFileList = [
    'node_modules/angular/angular.min.js',
    'node_modules/angular-ui-router/build/angular-ui-router.min.js',
    'node_modules/angular-resource/angular-resource.min.js',
    'node_modules/sweetalert/dist/sweetalert.min.js',
    'assets/js/admin-app.js',
    'assets/js/admin-routes.js',
    'assets/js/stripe-factory.js',
    'assets/js/user-factory.js',
];

gulp.task( 'sass', function() {
    gulp.src('./assets/scss/wp-stripe-styles.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./build/css'));
});

gulp.task( 'js', function(){
    gulp.src(jsFileList)
        .pipe(concat('wp-stripe-scripts.js'))
        .pipe(gulp.dest('build/js/'))
});

gulp.task( 'watch', function(){
    gulp.watch('./assets/scss/*.scss', ['sass'] );
    gulp.watch(jsFileList, ['js'] );
})

gulp.task( 'default', ['sass', 'js', 'watch'] );