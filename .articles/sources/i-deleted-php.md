---
timestamp: 1507943214291
title: I deleted PHP
slug: i-deleted-php
---
The most expensive server is the one doing nothing. And that was basically happening with this blog. I always had some kind of shared hosting or VPS but it doesn't really make sense. It's just a blog and - let's be honest - not that popular. How expensive is to take care of such a website? Well, currently I pay about $7 USD (basically only AWS Lightsail + VAT + S3 backups) per month.

But not anymore! I rewrited this blog from old and not very flexible PHP code to new and modern JS code with great focus on serverless technologies. This is how I reduced the expenses to zero USD. And also - I deleted my last project written in PHP running in production. Now I am completely JS guy... :D

# Technologies behind
This blog runs on [Next.js 4](https://zeit.co/blog/next4) with React 16. I could write it in pure JS - sure. But first of all I don't want to invest to much money and my time into this website and second of all. Next.js is just awesome. Really. Probably the best thing about this framework is server side rendering (SSR) by default. And this is huge for me. It basically means that this blog **works even with JS disabled in browser** because it's prerendered on server.

Another quite cool thing is link prefetch. This is one of the things I always missed in PHP. You have great website, you already know where will user most probably click but it's super hard to prefetch this page. But not with Next.js. With this framework it's just one word in the link - `prefetch`:

```js
<Link prefetch href="/archive">blog</Link>
```

Just try it. Delete all the cache, go to the homepage and click on the word "blog" in the text. Immediate response. Not even blink. Wow. Now in the archive click on the fist highlighted article. Bam - immediate response. This is because it's already downloaded and browser just replaces the DOM with new page. Amazing.

And that's it. No database, no servers, just JS.

# Why did I delete the database
This was part of the plan. I wanted to simplify this blog. It was to complicated - with administration and everything. And I don't really have time to maintain it (or finish it to be honest). Therefore I had to face the reality a simplify it as much as possible.

I originally wanted to use DynamoDB but that's not really applicable in this scenario because I need more advanced functions like sorting by date and so on. So instead of finding better engine, I decided to use just [pure MD files](https://github.com/mrtnzlml/zlml.cz/tree/master/.articles/sources). Everytime (after I write new article) I just have to recompile it (yeah that's not really friendly). Why MD files? It reflects how I write articles. I always open IDE and next to the code I write the ideas. Even this article is from IDE. It's crazy, but who cares - really.

So during the compilation it generates all the necessary files. It actually generates one new React component per article **with tests** and everything. After this I just have to push it and it's deployed automatically thanks to Serverless framework.

Oh and yes - it's ugly. I know. But I don't have time to care. Maybe later - it's MVP... :)
