// @flow

import Layout from '../components/Layout';
import Logo from '../components/Logo';
import ArticlesDatabase from '../.articles/.articleTitles.json';

import Link from '../components/markup/Link';

type Props = {
  articles: Array<Object>,
};

const Archive = ({ articles }: Props) => (
  <Layout>
    <Logo />

    {articles.map(article => (
      <div key={article.id}>
        <Link
          href={{ pathname: 'article', query: { id: article.id } }}
          as={article.slug}
        >
          {article.title}
        </Link>
      </div>
    ))}
  </Layout>
);

Archive.getInitialProps = async function() {
  const articles = [];
  for (const article of ArticlesDatabase) {
    articles.push({
      id: article.id,
      title: article.title,
      slug: article.slug,
    });
  }
  return {
    articles: articles,
  };
};

export default Archive;
