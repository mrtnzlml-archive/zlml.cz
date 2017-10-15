// @flow

import React from 'react';
import renderer from 'react-test-renderer';

import Link from '../Link';

it('renders correctly relative links using Next.js', () => {
  // contains additional 'onClick' prop
  expect(renderer.create(<Link href="/about" />).toJSON()).toMatchSnapshot();
  expect(renderer.create(<Link href="archive" />).toJSON()).toMatchSnapshot();
});

it('renders correctly absolute links using HTML', () => {
  // doesn't contain 'onClick' prop by default
  expect(
    renderer.create(<Link href="https://nomadlist.com/@mrtnzlml" />).toJSON(),
  ).toMatchSnapshot();
  expect(
    renderer.create(<Link href="https://twitter.com/mrtnzlml" />).toJSON(),
  ).toMatchSnapshot();
});
