// @flow

import * as React from 'react';

const style = {
  fontSize: '2rem',
  textAlign: 'justify',
  hyphens: 'auto',
};

type Props = {
  children: React.Node,
};

export default function Paragraph({ children }: Props) {
  return <p style={style}>{children}</p>;
}
