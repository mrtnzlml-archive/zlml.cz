const fs = require('fs');
const fm = require('front-matter');

let allArticles = [];
let filenames = fs.readdirSync(__dirname);

filenames.forEach(function(filename) {
  if (!!filename.match(/.md$/)) {
    let content = fs.readFileSync(`${__dirname}/${filename}`, 'utf-8');
    allArticles.push(fm(content));
  }
});

console.error(allArticles);
allArticles = allArticles.sort((a, b) => {
  if (a.attributes.timestamp < b.attributes.timestamp) return +1;
  if (a.attributes.timestamp > b.attributes.timestamp) return -1;
  return 0;
});

console.error(allArticles);
fs.writeFileSync(`${__dirname}/.database.json`, JSON.stringify(allArticles));
