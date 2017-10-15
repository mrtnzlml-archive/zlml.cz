// @flow

import React from 'react';
import NextLink from 'next/link';

import Colors from '../../services/Colors';

type Props = {
  href: string,
  children: React.Children,
  // may contain other props (it's not exact type)
};

export default function Link(props: Props) {
  let isExternal = false;
  if (/^https?:\/\//i.test(props.href)) {
    isExternal = true;
  }
  return (
    <span>
      <style jsx global>{`
        a,
        a:hover,
        a:focus {
          color: ${Colors.red};
          text-decoration: none;
        }

        a:hover,
        a:focus {
          text-decoration: none;
          color: #fff;
          background: ${Colors.red};
        }
      `}</style>
      {isExternal ? (
        <a {...props}>{props.children}</a>
      ) : (
        <NextLink {...props}>
          <a>{props.children}</a>
        </NextLink>
      )}
    </span>
  );
}
