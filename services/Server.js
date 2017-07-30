process.env.NODE_ENV = 'development';

const Application = require('./Application');

Application.app
  .prepare()
  .then(() => {
    Application.createExpressApp().listen(3000, err => {
      if (err) throw err;
      console.log('> Ready on http://localhost:3000');
    });
  })
  .catch(ex => {
    console.error(ex.stack);
    process.exit(1);
  });
