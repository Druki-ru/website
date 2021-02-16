import {theme as compileThemeCss} from "./css/compile";
import {theme as watchThemeCss} from "./css/watch";
import {
  modules as compileModulesJs,
  theme as compileThemeJs
} from "./js/compile";
import {modules as watchModulesJs, theme as watchThemeJs} from "./js/watch";
import {parallel} from 'gulp';

export const compile = parallel(compileThemeCss, compileThemeJs, compileModulesJs);
export const watch = parallel(watchThemeCss, watchThemeJs, watchModulesJs);
