// @flow

import * as React from 'react';

type Props = {
  children: React.Node,
};

export default function Strong(props: Props) {
  return <strong {...props} />;
}
