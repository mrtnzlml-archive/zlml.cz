// @flow

process.env.NODE_ENV = 'production';

const ASE = require('aws-serverless-express');
const Application = require('./Application');

const server = ASE.createServer(Application.createExpressApp());

// TODO: transpilovat (a nejlépe zabalit)

exports.handler = (
  event: Object,
  context: ?Object,
  callback: (error: null, success: string) => void,
) => {
  /** Immediate response for WarmUP plugin */
  if (event.source === 'serverless-plugin-warmup') {
    return callback(null, 'Lambda is warm!');
  }

  return ASE.proxy(server, event, context);
};
