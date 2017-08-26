// @flow

process.env.NODE_ENV = 'production';

const ASE = require('aws-serverless-express');
const Application = require('./Application');

const server = ASE.createServer(Application.createExpressApp());

// $FlowFixMe (this file is not transpiled therefore it's not possible to use Flow)
exports.handler = (event, context, callback) => {
  /** Immediate response for WarmUP plugin */
  if (event.source === 'serverless-plugin-warmup') {
    return callback(null, 'Lambda is warm!');
  }

  return ASE.proxy(server, event, context);
};
