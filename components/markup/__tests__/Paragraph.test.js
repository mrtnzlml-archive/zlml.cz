// @flow

import * as React from 'react';
import renderer from 'react-test-renderer';

import Paragraph from '../Paragraph';

it('renders correctly', () => {
  expect(
    renderer.create(<Paragraph>TEST</Paragraph>).toJSON(),
  ).toMatchSnapshot();
});
