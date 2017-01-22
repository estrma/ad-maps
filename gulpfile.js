var gulp = require("gulp");
var babel = require("gulp-babel");
var minify = require('gulp-minify');
var livereload = require('gulp-livereload');
var sass = require('gulp-sass');


gulp.task('watch', function() {
  livereload.listen();
  gulp.watch("assets/js/app.js", ['scripts']);
  gulp.watch("assets/scss/style.scss", ['sass']);
});

gulp.task('scripts', function () {
  return gulp.src("assets/js/app.js")
    .pipe(babel({
			presets: ['es2015']
		}))
    .pipe(minify({ 
        ext:{
            min:'.min.js'
        },
        exclude: ['tasks']
    }))
    .pipe(gulp.dest("ad-maps/lib/assets/js"))
    .pipe(livereload())
  ;
});

gulp.task('sass', function () {
  return gulp.src("assets/scss/style.scss")
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest("ad-maps/lib/assets/css"))
    .pipe(livereload())
  ;
});


gulp.task('default', ['sass', 'scripts'], function() {
    livereload.listen();


    gulp.watch(['assets/scss/style.scss'], ['sass']);
    gulp.watch(['assets/js/app.js'], ['scripts']);

    gulp.watch(['ad-maps/lib/**/*.php', 'ad-maps/lib/templates/*.twig']).on('change', function(file){
        livereload.changed(file.path);
    });

});


