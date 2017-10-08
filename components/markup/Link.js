// @flow

import NextLink from 'next/link';

import Colors from '../../services/Colors';

export default function Link(props: Object) {
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
          text-decoration: underline;
        }
      `}</style>
      <NextLink {...props}>
        <a>{props.children}</a>
      </NextLink>
    </span>
  );
}
