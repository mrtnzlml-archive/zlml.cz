process.env.NODE_ENV = 'production';

const awsServerlessExpress = require('aws-serverless-express');
const Application = require('./Application');

const server = awsServerlessExpress.createServer(
  Application.createExpressApp()
);

exports.handler = (event, context) =>
  awsServerlessExpress.proxy(server, event, context);
