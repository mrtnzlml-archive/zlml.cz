// @flow

import Link from 'next/link';

import Layout from '../components/Layout';
import Logo from '../components/Logo';

export default () =>
  <Layout>
    <Link prefetch href="/archive">
      <a>
        Looking for a <strong>blog</strong>? It&apos;s here!
      </a>
    </Link>
    <p>¡Hola! My name is</p>
    <Logo />
    <p>
      And I love modern JS and clever ideas. I work with ES2017+ (and Flow and
      GraphQL) on a daily basis and I really enjoy it.
    </p>
    <p>
      I am also very interested in serverless technologies and that&apos;s why
      this blog runs on AWS Lambda environment.
    </p>
    <p>
      I&apos;ve started as a PHP programmer {new Date().getFullYear() - 2011}{' '}
      years ago and even though I love PHP, I do enjoy modern JS environment
      much more.
    </p>
    <p>
      My &hearts; beats for open-source and testable code. Have a look{' '}
      <a href="https://github.com/mrtnzlml/">at my work</a> (unfortunately most
      of my work is in private Kiwi.com Gitlab).
    </p>
    {/* TODO: co dělám (kiwi.com) a co ještě umím (NoSQL, Doctrine, ...) */}
    <p>
      Do you want to stay in touch with me? That&apos;s great and I really
      appreciate it!
    </p>
    <p>mrtnzlml@gmail.com</p>
    <p>
      Become one of my awesome
      <a href="https://twitter.com/mrtnzlml">
        TODO <del>unicorns</del> followers
      </a>
      on Twitter&hellip;
    </p>
    <p>
      <Link prefetch href="/archive">
        <a>
          Looking for a <strong>blog</strong>? It&apos;s here!
        </a>
      </Link>
    </p>
  </Layout>;
