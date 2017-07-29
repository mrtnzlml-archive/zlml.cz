const fs = require('fs');
// const uuid = require('uuid/v4');

fs
  .readFileSync('./.articles.txt')
  .toString()
  .split('\n')
  .forEach(function(line) {
    const article = line.match(
      /\(([0-9]+),'(.+?)','(.+?)','(.+?)','(.+?)',([0|1]),'(.+?)',([0|1])\)/,
    );
    console.log(unfuck(article[2])); // title

    fs.writeFileSync(
      article[3] + '.md',
      texy2markdown(unfuck(article[4])),
    );
  });

function unfuck(str) {
  str = str.replace(/(\\r)?\\n/g, '\n');

  // stripslashes - http://locutus.io/php/strings/stripslashes/
  return (str + '').replace(/\\(.?)/g, function(s, n1) {
    switch (n1) {
      case '\\':
        return '\\';
      case '0':
        return '\u0000';
      case '':
        return '';
      default:
        return n1;
    }
  });
}

function texy2markdown(s) {
  s = s.replace(/"([^"]+)":\[([^\]]+)\]/g, '[$1]($2)'); // links with []
  s = s.replace(/"([^"]+)":((?!\[)[^\s]+[^:);,.!?\s])/g, '[$1]($2)'); // links without []
  s = s.replace(/\[([^\[\]\|]+)\|([^\[\]\|]+)\]/g, '[$1]($2)'); // [doc|links]
  s = s.replace(/^(\d+)\) /gm, '$1. '); // bullets
  s = s.replace(/\[\* *(\S+) *(?:\.\(([^)]+)\) *)?\*\]/g, '![$2]($1)'); // images
  s = s.replace(/^\/--+([^]*?)^\\--+/gm, '```$1```'); // /--
  s = s.replace(/^```code\n/gm, '```\n');
  s = s.replace(/^```code (.+)/gm, '```$1');
  return s;
}
