// @flow

const express = require('express');
const next = require('next');

const dev = process.env.NODE_ENV !== 'production';
const app = next({ dev });
const handle = app.getRequestHandler();

const createExpressApp = () => {
  const expressApp = express();

  const singlePages = ['/archive', '/rss'];
  singlePages.forEach(page => {
    expressApp.get(page, (req, res) => {
      app.render(req, res, page);
    });
  });

  expressApp.get('/:slug', (req, res) => {
    app.render(req, res, `/p/${req.params.slug}`);
  });

  expressApp.get('*', (req, res) => {
    return handle(req, res);
  });

  return expressApp;
};

exports.createExpressApp = createExpressApp;
exports.app = app;
