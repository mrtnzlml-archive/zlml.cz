// @flow

import Link from 'next/link';

import Colors from '../services/Colors';

export default () => (
  <h1>
    <Link prefetch href="/">
      <a>Martin Zlámal</a>
    </Link>
    <style jsx>{`
      h1 a,
      h1 a:hover,
      h1 a:focus,
      h1 a:visited {
        color: ${Colors.red};
        text-decoration: none;
        font-size: 10rem;
      }
    `}</style>
  </h1>
);
