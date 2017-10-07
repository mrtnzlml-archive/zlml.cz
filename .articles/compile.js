const fs = require('fs');
const fm = require('front-matter');
const marked = require('marked');

const renderer = new marked.Renderer();
renderer.heading = function(text, level) {
  const actualLevel = level + 1;
  const escapedHeading = text.toLowerCase().replace(/[^\w]+/g, '-');
  return `<h${actualLevel} id="${escapedHeading}">${text} <a href="#${escapedHeading}">#</a></h${actualLevel}>`;
};

let allArticleTitles = [];

const sourcesDirectory = `${__dirname}/sources`;
fs.readdirSync(sourcesDirectory).forEach(function(filename) {
  if (!!filename.match(/.md$/)) {
    let content = fm(
      fs.readFileSync(`${sourcesDirectory}/${filename}`, 'utf-8'),
    );
    allArticleTitles.push(content.attributes);
    console.error(`> ${content.attributes.title}`);

    delete content.frontmatter;
    content.body = marked(content.body, { renderer: renderer })
    fs.writeFileSync(
      `${__dirname}/compiled/${content.attributes.slug}.js`,
      `export default ${JSON.stringify(content, null, 2)}\n`,
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
