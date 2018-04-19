// @flow

import * as React from 'react';
import PropTypes from 'prop-types';

import Layout from '../components/Layout';
import Logo from '../components/Logo';
import Link from '../components/markup/Link';
import Paragraph from '../components/markup/Paragraph';
import Strong from '../components/markup/Strong';
import Colors from '../services/Colors';

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
  children: PropTypes.node.isRequired,
};

export default function Homepage() {
  return (
    <Layout>
      <Logo />
      <SectionHeading>What I do</SectionHeading>
      <Paragraph>
        I love modern JS, clever ideas and minimalism. I work with{' '}
        <Strong>ES2017+</Strong> (and <Strong>Flow</Strong> and{' '}
        <Strong>GraphQL</Strong>) on a daily basis and I really enjoy it. I am
        also very interested in <Strong>serverless technologies</Strong> and
        that&apos;s why this website runs on <Strong>AWS Lambda</Strong>{' '}
        environment.
      </Paragraph>
      <Paragraph>
        I&apos;ve started as a PHP programmer {new Date().getFullYear() - 2011}{' '}
        years ago and even though I love PHP (and Hack) so much, I do enjoy
        modern JS environment much more&hellip;
      </Paragraph>

      <SectionHeading>What I love</SectionHeading>
      <Paragraph>
        My <Strong style={{ color: Colors.red }}>&hearts;</Strong> beats for
        open-source and testable code. Have a look{' '}
        <Link href="https://github.com/mrtnzlml/">at my work</Link>. This is why{' '}
        <Strong>I am not looking for a new job</Strong> - I work for{' '}
        <Link href="https://www.kiwi.com/">Kiwi.com</Link>.
      </Paragraph>
      <Paragraph>
        After my studies I decided to travel. You can probably find me{' '}
        <Link href="https://nomadlist.com/@mrtnzlml">
          somewhere on this planet
        </Link>. Do you want to stay in touch with me? That&apos;s great and I
        really appreciate it! You can write me an email:{' '}
      </Paragraph>
      <Paragraph>
        <code>mrtnzlml+readme@gmail.com</code>
      </Paragraph>
    </Layout>
  );
}
