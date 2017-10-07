const fs = require('fs');
const fm = require('front-matter');

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
