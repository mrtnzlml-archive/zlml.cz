const fs = require('fs');
const fm = require('front-matter');
const marked = require('marked');
const hljs = require('highlight.js');

/**
 * Copy-pasted from Marked.
 */
function escape(html, encode) {
  return html
    .replace(!encode ? /&(?!#?\w+;)/g : /&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

const renderer = new marked.Renderer();
renderer.heading = function(text, level) {
  const actualLevel = level + 1;
  const escapedHeading = text.toLowerCase().replace(/[^\w]+/g, '-');
  return `<h${actualLevel} id="${escapedHeading}">${text} <a href="#${
    escapedHeading
  }">#</a></h${actualLevel}>`;
};
renderer.code = function(code, lang, escaped) {
  const out = this.options.highlight(code, lang);
  if (out != null && out !== code) {
    escaped = true;
    code = out;
  }

  if (!lang) {
    return (
      '<pre><code class="hljs">' +
      (escaped ? code : escape(code, true)) +
      '\n</code></pre>'
    );
  }

  return (
    '<pre><code class="hljs ' +
    this.options.langPrefix +
    escape(lang, true) +
    '">' +
    (escaped ? code : escape(code, true)) +
    '\n</code></pre>\n'
  );
};

let allArticleTitles = [];

const sourcesDirectory = `${__dirname}/sources`;
fs.readdirSync(sourcesDirectory).forEach(function(filename) {
  if (!!filename.match(/.md$/)) {
    const content = fm(
      fs.readFileSync(`${sourcesDirectory}/${filename}`, 'utf-8'),
    );
    delete content.frontmatter;
    content.body = marked(content.body, {
      renderer: renderer,
      gfm: true,
      tables: true,
      breaks: false,
      pedantic: false,
      sanitize: false,
      smartLists: true,
      smartypants: false,
      highlight: function(code, lang) {
        return hljs.highlightAuto(code, [lang]).value;
      },
    });

    allArticleTitles.push(
      Object.assign({}, content.attributes, {
        preview: content.body.replace(/<[^>]+>/g, '').substr(0, 150) + '...',
      }),
    );
    console.error(`> ${content.attributes.title}`);

    // article
    fs.writeFileSync(
      `${__dirname}/../pages/p/${content.attributes.slug}.js`,
      `// @flow\n\nimport WithPost from '../../components/WithPost';\n\nexport default WithPost(${JSON.stringify(
        content,
        null,
        2,
      )});\n`,
    );

    // test for this article
    fs.writeFileSync(
      `${__dirname}/../pages/p/__tests__/${content.attributes.slug}.test.js`,
      `// @flow

import React from 'react';
import renderer from 'react-test-renderer';
import Router from 'next/router';

// Workaround for "No router instance found. You should only use "next/router" inside the client side of your app."
Router.router = {
  prefetch: () => {},
};

import Article from '../${content.attributes.slug}';

it('renders correctly', () => {
  expect(renderer.create(<Article />).toJSON()).toMatchSnapshot();
});
`,
    );
  }
});

allArticleTitles = allArticleTitles.sort((a, b) => {
  if (a.timestamp < b.timestamp) return +1;
  if (a.timestamp > b.timestamp) return -1;
  return 0;
});

fs.writeFileSync(
  `${__dirname}/archive.json`,
  JSON.stringify(allArticleTitles, null, 2),
);
