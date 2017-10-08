// @flow

const style = {
  fontSize: '2rem',
  textAlign: 'justify',
  hyphens: 'auto',
};

export default function Paragraph({ children }: { children: Object }) {
  return <p style={style}>{children}</p>;
}
