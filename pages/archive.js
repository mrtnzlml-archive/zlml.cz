// @flow

import * as React from 'react';

import Layout from '../components/Layout';
import Logo from '../components/Logo';
import Link from '../components/markup/Link';
import Strong from '../components/markup/Strong';
import Paragraph from '../components/markup/Paragraph';

import ArticlesDatabase from '../.articles/archive.json';

type Props = {
  articles: Array<{|
    title: string,
    slug: string,
    date: string,
  |}>,
};

const Archive = ({ articles }: Props) => (
  <Layout title="Archive">
    <Logo />

    <Paragraph>
      <Strong>🚶 This blog has been discontinued!</Strong>
    </Paragraph>

    {articles.map((article, index) => {
      return (
        <div key={article.slug}>
          <span style={{ color: '#aaa', paddingRight: '1rem' }}>
            {article.date}
          </span>
          <Link
            href={`/p/${article.slug}`}
            as={article.slug}
            prefetch={index === 0} // prefetch first article
          >
            {article.title}
          </Link>
        </div>
      );
    })}
  </Layout>
);

Archive.getInitialProps = async function(): Promise<Props> {
  const articles = [];
  for (const article of ArticlesDatabase) {
    articles.push({
      title: article.title,
      slug: article.slug,
      date: new Date(article.timestamp).toISOString().slice(0, 10),
    });
  }
  return {
    articles: articles,
  };
};

export default Archive;
