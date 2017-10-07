// @flow

import Layout from '../components/Layout';
import Logo from '../components/Logo';

import Link from '../components/markup/Link';
import Paragraph from '../components/markup/Paragraph';
import Strong from '../components/markup/Strong';

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
        .article blockquote {
          margin-left: 1rem;
          padding-left: 1rem;
          font-style: italic;
          border-left: 5px solid #ccc;
        }
        .article p {
          hyphens: auto;
          text-align: justify;
        }
        .article pre {
          overflow-y: auto;
          line-height: 1.2;
          padding: 1.5rem;
          background-color: #f7f7f7;
          overflow-x: auto;
          border-left: 1px dashed #ddd;
        }
      `}</style>
      <LinkBack />
      <h1>{article.attributes.title}</h1>
      <div
        className="article"
        dangerouslySetInnerHTML={{
          __html: article.body,
        }}
      />
      <Paragraph>
        {/* TODO: Twitter share link: */}
        <Strong>
          Do you have comments? That&apos;s great! Tweet them so everyone can
          hear you&hellip;
        </Strong>
      </Paragraph>
      <LinkBack />
    </Layout>
  );
};

Article.getInitialProps = async function(context) {
  const { slug } = context.query;
  // TODO: 404
  // FIXME: it's still downloading all the articles (why?)
  return {
    // eslint-disable-next-line import/no-dynamic-require
    article: require(`${__dirname}/../.articles/compiled/${slug}.js`).default,
  };
};

export default Article;
