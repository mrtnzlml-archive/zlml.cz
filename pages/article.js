// @flow

import marked from 'marked';

import Layout from '../components/Layout';
import Logo from '../components/Logo';

import Link from '../components/markup/Link';
import Paragraph from '../components/markup/Paragraph';

const renderer = new marked.Renderer();
renderer.heading = function(text, level) {
  const actualLevel = level + 1;
  const escapedHeading = text.toLowerCase().replace(/[^\w]+/g, '-');
  return `<h${actualLevel} id="${escapedHeading}">${text} <a href="#${escapedHeading}">#</a></h${actualLevel}>`;
};

type Props = {
  article: Object,
};

const LinkBack = () => (
  <Paragraph>
    <Link prefetch href="/archive">
      &larr; back to the archive
    </Link>
  </Paragraph>
);

const Article = ({ article }: Props) => {
  return (
    <Layout>
      <Logo />
      <style jsx global>{`
        .article p {
          hyphens: auto;
          text-align: justify;
        }
        .article pre {
          overflow-y: auto;
        }
      `}</style>
      <LinkBack />
      <h1>{article.attributes.title}</h1>
      <div
        className="article"
        dangerouslySetInnerHTML={{
          __html: marked(article.body, { renderer: renderer }),
        }}
      />
      <Paragraph>Comments? Twitter!</Paragraph>
      <LinkBack />
    </Layout>
  );
};

Article.getInitialProps = async function(context) {
  const { slug } = context.query;
  // TODO: 404
  return {
    // eslint-disable-next-line import/no-dynamic-require
    article: require(`${__dirname}/../.articles/compiled/${slug}.js`).default,
  };
};

export default Article;
