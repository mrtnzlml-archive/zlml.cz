// @flow

const style = {
  fontSize: '2rem',
  textAlign: 'justify',
  hyphens: 'auto',
};

export default ({ children }: { children: Object }) => (
  <p style={style}>{children}</p>
);
