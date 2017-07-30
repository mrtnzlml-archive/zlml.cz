import Layout from '../components/Layout';
import Logo from '../components/Logo';
import marked from 'marked';

export default () =>
  <Layout>
    <Logo />
    <div dangerouslySetInnerHTML={{__html: marked('**article**\n# Title')}} />
  </Layout>;
