// @flow

import PropTypes from 'prop-types';
import Link from 'next/link';

import Layout from '../components/Layout';
import Logo from '../components/Logo';
import Paragraph from '../components/markup/Paragraph';
import Strong from '../components/markup/Strong';

const SectionHeading = ({ children }) => (
  <div
    style={{
      textTransform: 'uppercase',
      color: '#aaa',
      fontSize: 'smaller',
      fontWeight: 'bold',
    }}
  >
    {children}
  </div>
);

SectionHeading.propTypes = {
  children: PropTypes.element.isRequired,
};

function Homepage() {
  return (
    <Layout>
      <Logo />
      <SectionHeading>What I do</SectionHeading>
      <Paragraph>
        I love modern JS, clever ideas and minimalism. I work with{' '}
        <Strong>ES2017+</Strong> (and <Strong>Flow</Strong> and{' '}
        <Strong>GraphQL</Strong>) on a daily basis and I really enjoy it. I am
        also very interested in <Strong>serverless technologies</Strong> and
        that&apos;s why this{' '}
        <Link prefetch href="/archive">
          <a>
            <Strong>blog</Strong>
          </a>
        </Link>{' '}
        runs on <Strong>AWS Lambda</Strong> environment.
      </Paragraph>
      <Paragraph>
        I&apos;ve started as a PHP programmer {new Date().getFullYear() - 2011}{' '}
        years ago and even though I love PHP (and Hack) so much, I do enjoy
        modern JS environment much more. It&apos;s probably because it&apos;s so
        dynamic.
      </Paragraph>

      <SectionHeading>What I love</SectionHeading>
      <Paragraph>
        My &hearts; beats for open-source and testable code. Have a look{' '}
        <a href="https://github.com/mrtnzlml/">at my work</a> (unfortunately
        most of my work is in private <Strong>Kiwi.com</Strong> Gitlab). This is
        why <Strong>I am not looking for a new job</Strong> - I love Kiwi.com.
      </Paragraph>
      <Paragraph>
        After my studies I decided to travel. You can probably find me{' '}
        <a href="https://nomadlist.com/@mrtnzlml">somewhere on this planet</a>.
        Do you want to stay in touch with me? That&apos;s great and I really
        appreciate it!{' '}
        <a href="https://twitter.com/mrtnzlml">Here is my Twitter account</a> so
        you can follow my journeys.
      </Paragraph>
      <Paragraph>
        <Link prefetch href="/archive">
          <a>
            Looking for a <strong>blog archive</strong>? It&apos;s here!
          </a>
        </Link>
      </Paragraph>
      <Paragraph>Here is your kitten for making it through:</Paragraph>
      <img
        style={{ width: '100%' }}
        src="https://s3-eu-west-1.amazonaws.com/zlmlcz-media/kitten.jpg"
      />
      <Paragraph>
        By the way - this website is also{' '}
        <a href="https://github.com/mrtnzlml/zlml.cz">open-sourced</a>.
      </Paragraph>
      <Paragraph>
        <Link href="/rss">
          <a>RSS</a>
        </Link>
      </Paragraph>
    </Layout>
  );
}

Homepage.propTypes = {
  children: PropTypes.element.isRequired,
};

export default Homepage;
