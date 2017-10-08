// @flow

import ReactGA from 'react-ga';

export const initGA = () => {
  if (process.env.NODE_ENV === 'test') {
    return;
  }
  ReactGA.initialize('UA-39142238-1');
};

export const logPageView = () => {
  if (process.env.NODE_ENV === 'test') {
    return;
  }
  ReactGA.set({ page: window.location.pathname + window.location.search });
  ReactGA.pageview(window.location.pathname + window.location.search);
};

// export const logEvent = (category: string = '', action: string = '') => {
//   if (category && action) {
//     ReactGA.event({ category, action });
//   }
// };

// export const logException = (
//   description: string = '',
//   fatal: boolean = false,
// ) => {
//   if (description) {
//     ReactGA.exception({ description, fatal });
//   }
// };
