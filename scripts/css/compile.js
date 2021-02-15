import {dest, src} from 'gulp';
import sourcemaps from 'gulp-sourcemaps';
import postcss from 'gulp-postcss';
import rename from 'gulp-rename';
import destinationToSource from "../utils/destinationToSource";
import {paths} from "../config";
import postCssPlugins from "./postCssPlugins";

/**
 * Compiles styles for theme.
 */
function theme() {
  return src([paths.theme.css + '/**/*.pcss'])
    .pipe(sourcemaps.init())
    .pipe(postcss(postCssPlugins))
    .pipe(rename({
      extname: '.css',
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(dest(destinationToSource));
}

export {
  theme,
}
