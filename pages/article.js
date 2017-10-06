// @flow

import marked from 'marked';

import Layout from '../components/Layout';
import Logo from '../components/Logo';

import ArticlesDatabase from '../.articles/.articleBodies.json';

type Props = {
  id: number,
};

const Article = ({ id }: Props) => {
  const article = ArticlesDatabase[id];
  return (
    <Layout>
      <Logo />
      <style jsx global>{`
        .article p {
          hyphens: auto;
          text-align: justify;
        }
      `}</style>
      <h1>{article.attributes.title}</h1>
      <div
        className="article"
        dangerouslySetInnerHTML={{ __html: marked(article.body) }}
      />
    </Layout>
  );
};

Article.getInitialProps = async function(context) {
  const { id } = context.query;
  return {
    id,
  };
};

export default Article;
