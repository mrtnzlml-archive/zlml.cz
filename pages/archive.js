// @flow

import ArticlesDatabase from '../.articles/archive.json';

import Layout from '../components/Layout';
import Logo from '../components/Logo';
import Link from '../components/markup/Link';
import Strong from '../components/markup/Strong';

type Props = {
  articles: Array<Object>,
};

const Archive = ({ articles }: Props) => (
  <Layout>
    <Logo />

    {articles.map((article, index) => {
      return (
        <div key={article.id}>
          <span style={{ color: '#aaa', paddingRight: '1rem' }}>
            {article.date}
          </span>
          <Link
            href={`/p/${article.slug}`}
            as={article.slug}
            prefetch={index === 0} // prefetch first article
          >
            {index === 0 ? <Strong>{article.title}</Strong> : article.title}
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
