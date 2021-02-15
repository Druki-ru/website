import {theme as compileThemeCss} from "./css/compile";
import {theme as watchThemeCss} from "./css/watch";
import {parallel} from 'gulp';

export const compile = parallel(compileThemeCss);
export const watch = parallel(watchThemeCss);
