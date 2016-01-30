var gulp = require('gulp'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat');

var jsFileList = [
    'node_modules/angular/angular.min.js',
    'node_modules/angular-ui-router/build/angular-ui-router.min.js',
    'node_modules/angular-resource/angular-resource.min.js',
    'node_modules/sweetalert/dist/sweetalert.min.js',
    'assets/js/stripe-wp-app.js',
    'assets/js/admin-routes.js',
    'assets/js/stripe-factory.js',
    'assets/js/user-factory.js',
    'assets/js/shortcode-inserter.js'
];

var jsFileListFED = [
    'node_modules/angular/angular.min.js',
    'node_modules/angular-ui-router/build/angular-ui-router.min.js',
    'node_modules/angular-resource/angular-resource.min.js',
    'node_modules/sweetalert/dist/sweetalert.min.js',
    'assets/js/stripe-wp-app.js',
    'assets/js/admin-routes.js',
    'assets/js/stripe-factory.js',
    'assets/js/user-factory.js',
]

gulp.task( 'sass', function() {
    gulp.src('./assets/scss/wp-stripe-styles.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./build/css'));
});

gulp.task( 'sassFED', function() {
    gulp.src('./assets/scss/wp-stripe-fed-styles.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./build/front-end/css'));
});

gulp.task( 'js', function(){
    gulp.src(jsFileList)
        .pipe(concat('wp-stripe-scripts.js'))
        .pipe(gulp.dest('build/js/'));
});

gulp.task( 'jsFED', function() {
    gulp.src(jsFileListFED)
        .pipe( concat('stripe-wp-fed-scripts.js') )
        .pipe( gulp.dest( './build/front-end/js' ) );
})

gulp.task( 'watch', function(){
    gulp.watch('./assets/scss/*.scss', ['sass', 'sassFED'] );
    gulp.watch(jsFileList, ['js'] );
    gulp.watch(jsFileListFED, ['jsFED'] );
})

gulp.task( 'default', ['sass', 'sassFED', 'js', 'jsFED', 'watch'] );