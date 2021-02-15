import {parallel, watch} from 'gulp';
import {theme as compileTheme} from './compile';
import {paths} from '../config';

/**
 * Watches for changes and recompile.
 */
function theme() {
  watch(paths.theme.css + '/**/*.pcss', parallel(compileTheme));
}

export {
  theme,
}
