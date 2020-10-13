const path = require('path');

module.exports = {
  entry: './src/index.js',
  output: {
    path: path.resolve(__dirname, 'assets/js'),
    filename: 'jorani.js',
    library: 'jorani',
    libraryTarget: 'var',
  }
};
