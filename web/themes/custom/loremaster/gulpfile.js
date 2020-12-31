const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const postcss = require('gulp-postcss');
const rename = require('gulp-rename');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const postcssCustomMedia = require('postcss-custom-media');
const postcssInlineSvg = require('postcss-inline-svg');
const postcssImport = require('postcss-import');
const postcssNesting = require('postcss-nesting');

gulp.task('build:css', function() {
  let plugins = [
    postcssImport(),
    postcssNesting(),
    postcssInlineSvg({
      paths: ['assets/images/icons']
    }),
    postcssCustomMedia({
      importFrom: 'css/01-generic/media-breakpoints.pcss',
    }),
    autoprefixer(),
    cssnano({ preset: 'default' }),
  ];

  return gulp.src([
    'css/**/*.pcss',
  ])
    .pipe(sourcemaps.init())
    .pipe(postcss(plugins))
    .pipe(rename({
      extname: '.css',
    }))
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest('dist/css/'));
});

gulp.task('watch:css', function() {
  gulp.watch('css/**/*.pcss', gulp.parallel('build:css'));
});
