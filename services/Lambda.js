process.env.NODE_ENV = 'production';

const awsServerlessExpress = require('aws-serverless-express');
const app = require('./Application');
const server = awsServerlessExpress.createServer(app);

exports.handler = (event, context) =>
  awsServerlessExpress.proxy(server, event, context);
