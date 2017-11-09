# Source code of zlml.cz

[![Greenkeeper badge](https://badges.greenkeeper.io/mrtnzlml/zlml.cz.svg)](https://greenkeeper.io/)

[![Build Status](https://travis-ci.org/mrtnzlml/zlml.cz.svg?branch=master)](https://travis-ci.org/mrtnzlml/zlml.cz)

# Install

```
git clone git@github.com:mrtnzlml/zlml.cz.git
cd zlml.cz
yarn install
```

And run it in development mode:

```
yarn dev
```

# Publish article

Create new MD file in `.articles/sources` with appropriate file headers and the article content. After finishing just run this command:

```
yarn run publish
```

It will generate new page with tests in `pages/p` folder. Now just push it to the Git and that's it...

# What's inside

- Next.js 4 with SSR
- React 16 (Fiber core)
- Flow

# Things to do

- get rid of `aws-serverless-express` - it's fucking leaking shit
- https://www.datadoghq.com/pricing/
