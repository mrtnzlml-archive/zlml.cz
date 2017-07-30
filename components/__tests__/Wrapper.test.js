// @flow

import React from 'react';
import renderer from 'react-test-renderer';

import Wrapper from '../Layout';

it('renders correctly', () => {
  expect(
    renderer.create(<Wrapper>WRAPPED</Wrapper>).toJSON(),
  ).toMatchSnapshot();
});
