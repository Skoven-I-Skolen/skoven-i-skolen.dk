const path = require('path');

const env = process.env.NODE_ENV || 'development';

const cssnanoOptions = {
  autoprefixer: true,
  discardComments: {
    removeAll: true,
  },
  mergeLonghand: true,
  colormin: false,
  zindex: false,
  discardUnused: {
    fontFace: false,
  },
};

const postcssPresetEnv = {
  importFrom: [
    path.join(__dirname, '../assets/components/css/base/custom-media.css'),
  ],
  stage: 3,
  features: {
    'custom-media-queries': true,
  },
};

module.exports = {
  env,
  map: env !== 'production' ? {
    inline: false,
  } : false,
  plugins: [
    /* eslint-disable global-require */
    require('stylelint')({
      ignoreFiles: [
        `${path.resolve('.')}/node_modules/**/*.css`,
      ],
    }),
    require('./postcss-plugins/postcss-nested-import')({
      postcssPresetEnv,
      cssnanoOptions,
    }),
    require('postcss-preset-env')(postcssPresetEnv),
    require('postcss-nested'),
    require('cssnano')(cssnanoOptions),
    require('postcss-reporter')({
      clearReportedMessages: true,
      throwError: false,
    }),
    /* eslint-enable global-require */
  ],
};
