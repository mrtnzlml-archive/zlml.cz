const fs = require('fs');
const fm = require('front-matter');

let allArticleTitles = [];
let allArticleBodies = {};

fs.readdirSync(__dirname).forEach(function(filename) {
  if (!!filename.match(/.md$/)) {
    let content = fm(fs.readFileSync(`${__dirname}/${filename}`, 'utf-8'));
    allArticleTitles.push(content.attributes);
    allArticleBodies[content.attributes.id] = content;
  }
});

console.error(allArticleTitles);
allArticleTitles = allArticleTitles.sort((a, b) => {
  if (a.timestamp < b.timestamp) return +1;
  if (a.timestamp > b.timestamp) return -1;
  return 0;
});

console.error(allArticleTitles);
fs.writeFileSync(
  `${__dirname}/.articleTitles.json`,
  JSON.stringify(allArticleTitles),
);
fs.writeFileSync(
  `${__dirname}/.articleBodies.json`,
  JSON.stringify(allArticleBodies),
);
