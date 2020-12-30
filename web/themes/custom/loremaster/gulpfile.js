const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const postcss = require('gulp-postcss');
const rename = require('gulp-rename');

gulp.task('build:css', function() {
  return gulp.src([
    'css/**/*.pcss',
  ])
    .pipe(sourcemaps.init())
    .pipe(postcss([
      // require('postcss-import'),
      require('postcss-inline-svg')({
        paths: ['assets/images/icons']
      }),
      require('postcss-custom-media')({
        importFrom: 'css/01-generic/media-breakpoints.pcss',
      }),
      require('autoprefixer'),
      require('cssnano')({
        preset: 'default'
      }),
    ]))
    .pipe(rename({
      extname: '.css',
    }))
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest('dist/css/'));
});

gulp.task('watch:css', function() {
  gulp.watch('css/**/*.pcss', gulp.parallel('build:css'));
});
