import vuePlugin from 'rollup-plugin-vue';
import resolve from 'rollup-plugin-node-resolve';
import nodeGlobals from 'rollup-plugin-node-globals';
import commonjs from 'rollup-plugin-commonjs';
import babel from 'rollup-plugin-babel';

export default {
  input: 'assets/js/src/main.js',
  external: ['vue', 'drupal'],
  output: {
    file: 'assets/js/dist/main.js',
    format: 'iife',
    globals: {
      vue: 'Vue',
      drupal: 'Drupal',
    },
  },
  plugins: [
    babel({
      exclude: 'node_modules/**'
    }),
    commonjs({
      include: 'node_modules/**',
    }),
    nodeGlobals(),
    resolve({
      preferBuiltins: true,
      jsnext: true,
      main: true,
      browser: true
    }),
    vuePlugin(),
  ],
};
