// @flow

import React from 'react';

import Layout from './Layout';
import Logo from './Logo';
import Link from './markup/Link';
import Paragraph from './markup/Paragraph';
import Strong from './markup/Strong';

const LinkBack = () => (
  <Paragraph>
    <Link prefetch href="/archive">
      &larr; back to the archive
    </Link>
  </Paragraph>
);

type Props = {
  attributes: {
    id: string,
    timestamp: number,
    title: string,
    slug: string,
  },
  body: string,
};

export default function WithPost(options: Props) {
  return class PostPage extends React.Component {
    render() {
      return (
        <Layout title={options.attributes.title}>
          <Logo />
          <style jsx global>{`
            .article blockquote {
              margin-left: 1rem;
              padding-left: 1rem;
              font-style: italic;
              border-left: 5px solid #ccc;
            }
            .article p {
              hyphens: auto;
              text-align: justify;
            }
            .article pre {
              overflow-y: auto;
              line-height: 1.2;
              padding: 1.5rem;
              background-color: #f7f7f7;
              overflow-x: auto;
              border-left: 1px dashed #ddd;
            }
          `}</style>
          <LinkBack />
          <h1>{options.attributes.title}</h1>
          <div
            className="article"
            dangerouslySetInnerHTML={{
              __html: options.body,
            }}
          />
          <Paragraph>
            <Strong>
              Do you have any comments? That&apos;s great!{' '}
              <Link
                href={`https://twitter.com/home?status=${encodeURIComponent(
                  `https://zlml.cz/${options.attributes.slug}`,
                )}%20cc%20%40mrtnzlml`}
              >
                Tweet them
              </Link>{' '}
              so everyone can hear you&hellip;
            </Strong>
          </Paragraph>
          <LinkBack />
        </Layout>
      );
    }
  };
}
