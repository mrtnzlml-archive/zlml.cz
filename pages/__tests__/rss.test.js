// @flow

import Rss from '../rss';

it('is not component', () => {
  expect(Rss()).toBe(undefined);
});

it('sends expected headers to the client', () => {
  expect.assertions(4);
  Rss.getInitialProps({
    res: {
      writeHead: (status, headers) => {
        expect(status).toBe(200);
        expect(headers).toMatchSnapshot();
      },
      write: body => {
        expect(body).toBeTruthy();
      },
      end: () => {
        expect(true).toBe(true); // expect it's called
      },
    },
  });
});
