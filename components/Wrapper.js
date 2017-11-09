// @flow

import * as React from 'react';

type Props = {
  children?: React.Node,
};

export default function Wrapper({ children }: Props) {
  return (
    <div className="wrapper">
      {children}
      <style jsx>{`
        .wrapper {
          background: white;
          max-width: 900px;
          margin: 0 auto;
          padding: 12px;
          padding-bottom: 36px;
          box-shadow: 0 1px 2px #aaa;
        }

        @media screen and (min-width: 720px) {
          .wrapper {
            padding-right: 36px;
            padding-left: 36px;
          }
        }
      `}</style>
    </div>
  );
}
