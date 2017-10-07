// @flow

const express = require('express');
const next = require('next');

const dev = process.env.NODE_ENV !== 'production';
const app = next({ dev });
const handle = app.getRequestHandler();

const createExpressApp = () => {
  const expressApp = express();

  expressApp.get('/archive', (req, res) => {
    const actualPage = '/archive';
    app.render(req, res, actualPage);
  });

  expressApp.get('/:slug', (req, res) => {
    const actualPage = '/article';
    app.render(req, res, actualPage, { slug: req.params.slug });
  });

  expressApp.get('*', (req, res) => {
    return handle(req, res);
  });

  return expressApp;
};

exports.createExpressApp = createExpressApp;
exports.app = app;
