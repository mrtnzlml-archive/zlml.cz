// @flow

import marked from 'marked';

import Layout from '../components/Layout';
import Logo from '../components/Logo';

type Props = {
  id: number,
};

const Article = ({ id }: Props) =>
  <Layout>
    <Logo />
    <div
      dangerouslySetInnerHTML={{ __html: marked(`${id} **article**\n# Title`) }}
    />
  </Layout>;

Article.getInitialProps = async function(context) {
  const { id } = context.query; // FIXME nefunguje úplně dobře (client vs. server routování)
  return {
    id,
  };
};

export default Article;
