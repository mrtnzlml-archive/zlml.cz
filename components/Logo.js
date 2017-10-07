// @flow

import Colors from '../services/Colors';
import Link from '../components/markup/Link';
import Paragraph from '../components/markup/Paragraph';

export default () => [
  <Paragraph key="pre-heading">¡Hola! My name is</Paragraph>,
  <h1 key="heading">
    <Link prefetch href="/">
      Martin Zlámal
    </Link>
    <style jsx>{`
      h1 {
        font-size: 5rem;
      }
      @media screen and (min-width: 720px) {
        h1 {
          font-size: 8rem;
        }
      }
      h1 a,
      h1 a:hover,
      h1 a:focus,
      h1 a:visited {
        color: ${Colors.red};
        text-decoration: none;
      }
    `}</style>
  </h1>,
];
