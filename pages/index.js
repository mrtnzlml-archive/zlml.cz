import Link from 'next/link';

import Layout from '../components/Layout';
import Logo from '../components/Logo';

export default () =>
  <Layout>
    <Link prefetch href="/archive">
      <a>
        Looking for a <strong>blog</strong>? It's here!
      </a>
    </Link>

    <p>¡Hola! My name is</p>

    <Logo />

    <p>
      And I love modern JS (working with ECMAScript 2017+ on a daily basis) and clever ideas&hellip;
      <br />
      {/* TODO: wording */}
      Even this website runs on ES8 and AWS Lambda...
      <br />
      I am doing it for about {new Date().getFullYear() - 2011} years and I really enjoy clean and testable code.
      <br />
      My &hearts; beats for open-source. Have a look at my work (unfortunately most of my work is in private Gitlab):
    </p>

    <a href="https://github.com/mrtnzlml/">
      github.com/<strong>mrtnzlml</strong>
      <br />
      This is my personal GitHub profile with packages usually
      <abbr title="Testbench is the only exception">
        without release schedule
      </abbr>. There you can see my activity.
    </a>
    <a href="https://github.com/adeira/">
      github.com/<strong>adeira</strong>
      <br />
      Adeira is my new namespace for open-source packages and projects with
      proper release management.
    </a>

    <p>
      As you can see my specialization is Nette Framework and there are also
      tons of Doctrine ORM stuff or React components. I am also familiar with
      NoSQL databases like Redis. As a proof look at my public presentations and
      talks. I just love to learn something new&hellip;
    </p>

    <p>
      Currently <strong>I am NOT</strong> looking for a new job. I am working
      for <a href="https://www.kiwi.com/us/">Kiwi.com</a> in the Prague office.
      My job is to create chatbot with artificial intelligence to help customer
      support with repetitive tasks. I am also working on GraphQL API proxy.
      Everything runs on Node.js using serverless technologies (AWS Lambda) and
      yes - I enjoy it! :)
    </p>

    <p>
      Do you want to stay in touch with me? That's great and I really appreciate
      it!
    </p>
    <p>mrtnzlml@gmail.com</p>

    <p>
      Become one of my awesome
      <a href="https://twitter.com/mrtnzlml">
        TODO <del>unicorns</del> followers
      </a>
      on Twitter&hellip;
    </p>

    <p>
      <Link prefetch href="/archive">
        <a>
          Looking for a <strong>blog</strong>? It's here!
        </a>
      </Link>
    </p>
  </Layout>;
