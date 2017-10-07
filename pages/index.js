// @flow

import Layout from '../components/Layout';
import Logo from '../components/Logo';

import Link from '../components/markup/Link';
import Paragraph from '../components/markup/Paragraph';
import Strong from '../components/markup/Strong';

export default () => {
  return (
    <Layout>
      <Paragraph>¡Hola! My name is</Paragraph>
      <Logo />
      <Paragraph>
        And I love modern JS and clever ideas. I work with{' '}
        <Strong>ES2017+</Strong> (and <Strong>Flow</Strong> and{' '}
        <Strong>GraphQL</Strong>) on a daily basis and I really enjoy it.
      </Paragraph>
      <Paragraph>
        I am also very interested in <Strong>
          serverless technologies
        </Strong>{' '}
        and that&apos;s why this blog runs on AWS Lambda environment.
      </Paragraph>
      <Paragraph>
        I&apos;ve started as a PHP programmer {new Date().getFullYear() - 2011}{' '}
        years ago and even though I love PHP, I do enjoy modern JS environment
        much more. It&apos;s probably because it&apos;s much more dynamic.
      </Paragraph>
      <Paragraph>
        My &hearts; beats for open-source and testable code. Have a look{' '}
        <a href="https://github.com/mrtnzlml/">at my work</a> (unfortunately
        most of my work is in private <Strong>Kiwi.com</Strong> Gitlab). This is
        why <Strong>I am not looking for a new job</Strong>.
      </Paragraph>
      <Paragraph>
        Do you want to stay in touch with me? That&apos;s great and I really
        appreciate it!{' '}
        <Link href="https://twitter.com/mrtnzlml">
          Here is my Twitter account
        </Link>{' '}
        so you can follow my journeys.
      </Paragraph>
      <Paragraph>
        <Link prefetch href="/archive">
          <a>
            Looking for a <strong>blog</strong>? It&apos;s here!
          </a>
        </Link>
      </Paragraph>
      <Paragraph>
        By the way - this website is also{' '}
        <a href="https://github.com/mrtnzlml/zlml.cz">open-sourced</a>.
      </Paragraph>
    </Layout>
  );
};
