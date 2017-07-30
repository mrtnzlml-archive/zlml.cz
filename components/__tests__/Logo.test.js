// @flow

import React from 'react';
import renderer from 'react-test-renderer';

import Logo from '../Logo';

it('renders correctly', () => {
  expect(renderer.create(<Logo />).toJSON()).toMatchSnapshot();
});
