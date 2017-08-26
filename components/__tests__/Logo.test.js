// @flow

import React from 'react';
import renderer from 'react-test-renderer';
import Router from 'next/router';

import Logo from '../Logo';

// Workaround for "No router instance found. You should only use "next/router" inside the client side of your app."
Router.router = {
  prefetch: () => {},
};

it('renders correctly', () => {
  expect(renderer.create(<Logo />).toJSON()).toMatchSnapshot();
});
