// @flow

import Layout from '../components/Layout';
import Logo from '../components/Logo';
import ArticlesDatabase from '../.articles/archive.json';

import Link from '../components/markup/Link';

type Props = {
  articles: Array<Object>,
};

const Archive = ({ articles }: Props) => (
  <Layout>
    <Logo />

    {articles.map(article => {
      return (
        <div key={article.id}>
          <span style={{ color: '#aaa', paddingRight: '1rem' }}>
            {article.date}
          </span>
          <Link
            href={{ pathname: 'article', query: { slug: article.slug } }}
            as={article.slug}
          >
            {article.title}
          </Link>
        </div>
      );
    })}
  </Layout>
);

Archive.getInitialProps = async function() {
  const articles = [];
  for (const article of ArticlesDatabase) {
    articles.push({
      id: article.id,
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
