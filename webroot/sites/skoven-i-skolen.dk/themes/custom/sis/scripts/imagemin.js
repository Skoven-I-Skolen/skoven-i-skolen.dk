const imageMin = require('imagemin');
const imageminPngquant = require('imagemin-pngquant');
const imageminSvgo = require('imagemin-svgo');

const path = require('path');
const chalk = require('chalk');
const argv = require('minimist')(process.argv.slice(2));

const fileMatchPath = argv.i || '';
const destinationPath = argv.o || '';

(async () => {
  const files = await imageMin([path.resolve(fileMatchPath)], {
    destination: path.resolve(destinationPath),
    plugins: [
      imageminPngquant(),
      imageminSvgo(),
    ],
  });
  files.forEach((element) => {
    // eslint-disable-next-line no-console
    console.log(chalk.yellow(`minified: ${path.basename(element.destinationPath)}`));
  });
})();
