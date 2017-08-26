// @flow

import Link from 'next/link';

import Layout from '../components/Layout';
import Logo from '../components/Logo';
import ArticlesDatabase from '../.articles/.database.json';

type Props = {
  articles: Array<Object>,
};

const Archive = ({ articles }: Props) =>
  <Layout>
    <Logo />

    {articles.map(article =>
      <Link
        key={article.id}
        href={{ pathname: 'article', query: { id: article.id } }}
        as={article.slug}
      >
        <a>
          {article.title}
        </a>
      </Link>,
    )}
  </Layout>;

Archive.getInitialProps = async function() {
  // TODO: await storage
  const articles = [];
  for (const article of ArticlesDatabase) {
    articles.push({
      id: article.attributes.id,
      title: article.attributes.title,
      slug: article.attributes.slug,
    });
  }
  return {
    articles: articles,
  };
};

export default Archive;
