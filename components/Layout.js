// @flow

import Head from 'next/head';
import NProgress from 'nprogress';
import Router from 'next/router';

import Wrapper from '../components/Wrapper';
import Colors from '../services/Colors';

Router.onRouteChangeStart = () => NProgress.start();
Router.onRouteChangeComplete = () => NProgress.done();
Router.onRouteChangeError = () => NProgress.done();

type Props = {
  children?: Object,
  title?: string,
};

export default ({ children, title = 'This is the default title' }: Props) => (
  <div>
    <Head>
      <title>{title}</title>
      <meta charSet="utf-8" />
      <meta name="viewport" content="initial-scale=1.0, width=device-width" />
      <link rel="icon" href="/static/favicon.ico" />
      {/* Do not use styled-jsx here: https://github.com/zeit/next.js/issues/885 */}
      <style>
        {`
          body {
            font-family: Helvetica, Arial, Sans-Serif;
            font-weight: 300;
            font-size: 1.8rem;
            background-color: #f9f9f9;
            margin: 0;
            color: ${Colors.dark};
          }

          html {
            font-size: 62.5%;
          }
        `}
      </style>
      <link rel="stylesheet" type="text/css" href="/static/nprogress.css" />
    </Head>

    <Wrapper>{children}</Wrapper>
  </div>
);
