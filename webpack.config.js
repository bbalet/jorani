const path = require('path');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');

module.exports = {
  entry: './src/index.js',
  output: {
    path: path.resolve(__dirname, 'assets/js'),
    filename: 'jorani.js',
    library: 'jorani',
    libraryTarget: 'var',
  },
  plugins: [
    // Strip all moment's locales except those supported by Jorani
    new MomentLocalesPlugin({
        localesToKeep: ['en','en-gb','fr','es','nl','de','it','ru','cs','uk','km','fa','vi','tr','zh-cn','el','pt','ar','hu','ca','ro','sk'],
    }),
],
};
