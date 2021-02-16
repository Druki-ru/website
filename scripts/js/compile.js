import {dest, src} from 'gulp';
import {paths} from "../config";
import sourcemaps from 'gulp-sourcemaps';
import terser from 'gulp-terser';
import rename from 'gulp-rename';
import destinationToSource from "../utils/destinationToSource";

/**
 * Default compilation process.
 */
function compile(basePath) {
  return src([basePath + '/**/*.es6.js'])
    .pipe(sourcemaps.init())
    .pipe(terser())
    .pipe(rename(function (path) {
      // Strip 'es6' part.
      path.basename = path.basename.replace('.es6', '');
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(dest(destinationToSource));
}

/**
 * Compiles JavaScript for theme.
 */
function theme() {
  return compile(paths.theme.js);
}

/**
 * Compiles JavaScript for modules.
 */
function modules() {
  return compile(paths.modules.custom);
}

export {
  theme,
  modules,
}
