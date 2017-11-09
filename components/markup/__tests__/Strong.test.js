// @flow

import * as React from 'react';
import renderer from 'react-test-renderer';

import Strong from '../Strong';

it('renders correctly', () => {
  expect(renderer.create(<Strong>TEST</Strong>).toJSON()).toMatchSnapshot();
  expect(
    renderer.create(<Strong style={{ color: 'green' }}>TEST</Strong>).toJSON(),
  ).toMatchSnapshot();
});
