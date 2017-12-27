// @flow

import _escape from 'lodash/escape';

import ArticlesDatabase from '../.articles/archive.json';

const Rss = () => {};

const Item = ({ title, link, description }) => `<item>
    <title>${title}</title>
    <link>${link}</link>
    <description>${description} (please read more on my website)</description>
  </item>`;

Rss.getInitialProps = ({ res }) => {
  const articles = [];
  for (const article of ArticlesDatabase) {
    articles.push({
      title: article.title,
      link: `https://zlml.cz/${article.slug}`,
      description: _escape(article.preview),
    });
  }

  res.writeHead(200, {
    'Content-Type': 'application/xml; charset=utf-8',
  });
  res.write(`<?xml version="1.0" encoding="UTF-8"?>
  <rss version="2.0">
    <channel>
      <title>Martin Zlámal [BLOG]</title>
      <link>https://zlml.cz/</link>
      <description>
        Articles about programming. Nette Framework, PHP, React, Node.js,
        Serverless and much more...
      </description>
      ${articles.map(article => Item(article)).join('\n')}
    </channel>
  </rss>
`);
  res.end();
};

export default Rss;
