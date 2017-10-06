// @flow

import Layout from '../components/Layout';
import Logo from '../components/Logo';

import Paragraph from '../components/markup/Paragraph';
import Link from '../components/markup/Link';

export default () => (
  <Layout>
    <Link prefetch href="/archive">
      Looking for a <strong>blog</strong>? It&apos;s here!
    </Link>
    <Paragraph>¡Hola! My name is</Paragraph>
    <Logo />
    <Paragraph>
      And I love modern JS and clever ideas. I work with ES2017+ (and Flow and
      GraphQL) on a daily basis and I really enjoy it.
    </Paragraph>
    <Paragraph>
      I am also very interested in serverless technologies and that&apos;s why
      this blog runs on AWS Lambda environment.
    </Paragraph>
    <Paragraph>
      I&apos;ve started as a PHP programmer {new Date().getFullYear() - 2011}{' '}
      years ago and even though I love PHP, I do enjoy modern JS environment
      much more. It&apos;s probably because it&apos;s much more dynamic.
    </Paragraph>
    <Paragraph>
      My &hearts; beats for open-source and testable code. Have a look{' '}
      <a href="https://github.com/mrtnzlml/">at my work</a> (unfortunately most
      of my work is in private Kiwi.com Gitlab).
    </Paragraph>
    {/* TODO: co dělám (kiwi.com) a co ještě umím (NoSQL, Doctrine, ...) */}
    <Paragraph>Right now I am traveling around the world.</Paragraph>
    <Paragraph>
      Do you want to stay in touch with me? That&apos;s great and I really
      appreciate it!{' '}
      <Link href="https://twitter.com/mrtnzlml">
        Here is my Twitter account
      </Link>{' '}
      so you can follow my jurney.
    </Paragraph>
    <Paragraph>
      <Link prefetch href="/archive">
        <a>
          Looking for a <strong>blog</strong>? It&apos;s here!
        </a>
      </Link>
    </Paragraph>
  </Layout>
);
