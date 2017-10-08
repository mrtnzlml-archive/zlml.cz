// @flow

import React from 'react';
import renderer from 'react-test-renderer';

import Wrapper from '../Layout';

it('renders correctly', () => {
  expect(
    renderer
      .create(
        <Wrapper>
          <span>WRAPPED</span>
        </Wrapper>,
      )
      .toJSON(),
  ).toMatchSnapshot();
});