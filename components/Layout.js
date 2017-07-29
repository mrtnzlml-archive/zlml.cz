import Head from 'next/head';

import Wrapper from '../components/Wrapper';
import Colors from '../services/Colors';

export default ({ children, title = 'This is the default title' }) =>
  <div>
    <Head>
      <title>
        {title}
      </title>
      <meta charSet="utf-8" />
      <meta name="viewport" content="initial-scale=1.0, width=device-width" />
      <style global jsx>{`
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
      `}</style>
    </Head>

    <Wrapper>
      {children}

      <footer>
        <p>By the way - this website is also <a href="https://github.com/mrtnzlml/zlml.cz">open-sourced</a>.</p>
      </footer>
    </Wrapper>
  </div>;
