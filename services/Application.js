const express = require('express');
const next = require('next');

const dev = process.env.NODE_ENV !== 'production';
const app = next({ dev });
const handle = app.getRequestHandler();

function createServer() {
  const server = express();
  // add middleware, custom routing, whatever
  server.get('*', (req, res) => handle(req, res));
  return server;
}

module.exports = createServer();
