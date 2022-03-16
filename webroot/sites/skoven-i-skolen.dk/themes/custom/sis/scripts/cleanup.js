const argv = require('minimist')(process.argv.slice(2));
const del = require('del');

if (argv.i.length > 0) {
  (async () => del([argv.i]))();
}
