import Colors from '../services/Colors';

export default () =>
  <h1>
    <a>
      Martin Zlámal
    </a>
    <style jsx>{`
      h1 a,
      h1 a:hover,
      h1 a:focus,
      h1 a:visited {
        color: ${Colors.red};
        text-decoration: none;
      }
    `}</style>
  </h1>;
