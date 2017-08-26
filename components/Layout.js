// @flow

import Head from 'next/head';
import stylis from 'stylis';

import Wrapper from '../components/Wrapper';
import Colors from '../services/Colors';

type Props = {
  children?: Object,
  title?: string,
};

export default ({ children, title = 'This is the default title' }: Props) =>
  <div>
    <Head>
      <title>
        {title}
      </title>
      <meta charSet="utf-8" />
      <meta name="viewport" content="initial-scale=1.0, width=device-width" />
      <link rel="icon" href="/static/favicon.ico" />
      {/* Do not use styled-jsx here: https://github.com/zeit/next.js/issues/885 */}
      <style>
        {stylis(
          '',
          `
          body {
            font-family: sans-serif;
            font-weight: 300;
            font-size: 1.6rem;
            background-color: #f9f9f9;
            margin: 0;
            color: ${Colors.dark};
          }

          html {
            font-size: 62.5%;
          }

          p {
            hyphens: auto;
          }

          a,
          a:hover,
          a:focus {
            color: ${Colors.red};
          }

          a:visited {
            color: darkred;
          }

          a:hover,
          a:focus {
            text-decoration: underline;
          }

          a[href^="#error:"] {
            background: red;
            color: white;
          }
          `,
        )}
      </style>
    </Head>

    <Wrapper>
      {children}

      <footer>
        <p>
          By the way - this website is also{' '}
          <a href="https://github.com/mrtnzlml/zlml.cz">open-sourced</a>.
        </p>
      </footer>
    </Wrapper>
  </div>;
