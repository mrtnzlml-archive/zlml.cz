process.env.NODE_ENV = 'production';

const awsServerlessExpress = require('aws-serverless-express');
const Application = require('./Application');

const server = awsServerlessExpress.createServer(
  Application.createExpressApp()
);

exports.handler = (event, context) => {
  /** Immediate response for WarmUP plugin */
  if (event.source === 'serverless-plugin-warmup') {
    console.log('WarmUP - Lambda is warm!');
    return callback(null, 'Lambda is warm!');
  }

  return awsServerlessExpress.proxy(server, event, context);
};
