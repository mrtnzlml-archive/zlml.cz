const fs = require('fs');

fs
  .readFileSync('./.articles.txt')
  .toString()
  .split('\n')
  .forEach(function(line) {
    const article = line.match(
      /\(([0-9]+),'(.+?)','(.+?)','(.+?)','(.+?)',([0|1]),'(.+?)',([0|1])\)/,
    );
    console.log(unfuck(article[2])); // title

    fs.writeFileSync('metadata.txt', 'TODO'); // TODO: vygenerovat metadata (slug, ID, ...) pro import do DynamoDB
  });
