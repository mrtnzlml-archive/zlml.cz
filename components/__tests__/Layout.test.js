// @flow

import React from 'react';
import renderer from 'react-test-renderer';

import Layout from '../Layout';

it('renders correctly', () => {
  expect(renderer.create(<Layout>CONTENT</Layout>).toJSON()).toMatchSnapshot();
});
