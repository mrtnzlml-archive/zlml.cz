// @flow

const express = require('express');
const next = require('next');
const Articles = require('../.articles/.articleTitles.json');

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
    const article = findBySlug(req.params.slug);
    if (article) {
      const queryParams = { id: article.id };
      app.render(req, res, actualPage, queryParams);
    } else {
      return handle(req, res);
    }
  });

  expressApp.get('*', (req, res) => {
    return handle(req, res);
  });

  return expressApp;
};

exports.createExpressApp = createExpressApp;
exports.app = app;

function findBySlug(slug) {
  // $FlowAllowNextLineWithExplanation: property `find` not found in CommonJS exports
  return Articles.find(article => article.slug === slug);
}
