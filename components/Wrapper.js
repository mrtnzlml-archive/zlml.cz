// @flow

type Props = {
  children?: Object,
};

export default ({ children }: Props) =>
  <div className="wrapper">
    {children}
    <style jsx>{`
      .wrapper {
        background: white;
        max-width: 1100px;
        margin: 0 auto;
        padding-right: 12px;
        padding-left: 12px;
        box-shadow: 0 1px 2px #aaa;
        margin-top: 12px;
      }

      @media screen and (min-width: 720px) {
        .wrapper {
          padding-right: 24px;
          padding-left: 24px;
        }
      }
    `}</style>
  </div>;
