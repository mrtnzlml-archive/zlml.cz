// @flow

import Link from 'next/link';

export default (props: Object) => {
  return (
    <Link {...props}>
      <a>{props.children}</a>
    </Link>
  );
};
