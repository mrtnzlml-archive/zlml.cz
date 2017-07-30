// @flow

import Link from 'next/link';

import Layout from '../components/Layout';
import Logo from '../components/Logo';

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
  return {
    articles: [
      {
        id: 1,
        title: 'AAA',
        slug: 'a-a-a',
      },
      {
        id: 2,
        title: 'BBB',
        slug: 'b-b-b',
      },
      {
        id: 3,
        title: 'CCC',
        slug: 'c-c-c',
      },
    ],
  };
};

export default Archive;
