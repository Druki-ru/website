import {parallel, watch} from 'gulp';
import {modules as compileModules, theme as compileTheme} from './compile';
import {paths} from '../config';

/**
 * Watches for changes and recompile.
 */
function theme() {
  watch(paths.theme.js + '/**/*.es6.js', parallel(compileTheme));
}

/**
 * Watches for changes and recompile.
 */
function modules() {
  watch(paths.modules.custom + '/**/*.es6.js', parallel(compileModules));
}

export {
  theme,
  modules,
}
