const gulp = require('gulp');
const autoprefixer = require('autoprefixer');
const cssnano = require('gulp-cssnano');
const postcss = require('gulp-postcss');
const rename = require('gulp-rename');

gulp.task('css', () => {
  return gulp.src('assets/sass/main.css')
    .pipe(postcss())
    .pipe(rename('main.min.css'))
    .pipe(cssnano())
    .pipe(gulp.dest('assets/css/'));
});

gulp.task('watch', ['css'], () => {
    gulp.watch('assets/sass/*.css', ['css']);
});

gulp.task('default', ['watch']);
