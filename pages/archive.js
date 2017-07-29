import Link from 'next/link';

import Layout from '../components/Layout';
import Logo from '../components/Logo';

export default () =>
  <Layout>
    <Logo />

    <Link href={{ pathname: 'article', query: { article: 1 } }}>
      <a>Article 1</a>
    </Link>
    <Link href={{ pathname: 'article', query: { article: 2 } }}>
      <a>Article 2</a>
    </Link>
    <Link href={{ pathname: 'article', query: { article: 3 } }}>
      <a>Article 3</a>
    </Link>
  </Layout>;
