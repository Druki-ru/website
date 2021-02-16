/**
 * Configuration file for some scripts.
 */

/**
 * The project root directory.
 */
const PROJECT_ROOT = '..';

/**
 * Settings for custom theme.
 */
const paths = {
  projectRoot: PROJECT_ROOT,
  theme: {
    base: PROJECT_ROOT + '/web/themes/custom/loremaster',
    css: PROJECT_ROOT + '/web/themes/custom/loremaster/assets/css',
    js: PROJECT_ROOT + '/web/themes/custom/loremaster/assets/js',
  },
  modules: {
    custom: PROJECT_ROOT + '/web/modules/custom',
  }
}

export {
  paths,
}
