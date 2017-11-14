---
timestamp: 1510619991384
title: Persist your GraphQL queries!
slug: persist-your-graphql-queries
---
GraphQL is very powerful and I really love it. But it's not really production ready by default. There is a lot of things you need to prepare and one of them is called persistent (or sometimes static) queries. Let's have a look at what is it all about and why should one care. In the following examples, I will use Relay Modern as a client framework.

# Motivation
You will very often hear that GraphQL is great because it reduces the amount of data transferred between client and server. This is indeed behavior you'll get by default. But I was always doubting this argument little bit. It because you will transfer fewer data from the server to client (compare to the REST approach) but you will send much MUCH more data from the client to the server just to get those data. And as we all know uploads are usually super slow compare to the downloads. Actually - engineers from New York Times revealed that the larges queries may have up to **20 kB** and that's quite a lot for one question (yes, just the query itself without response). And I can imagine even bigger queries...

How is it even possible? Well, very easily. With Relay, you can compose a lot of components (GraphQL fragments respectively) which results in really large GraphQL query (even with all the optimizations). You are just describing that you really need all those fields and there is nothing you can do about such query (assuming you are already using fragments).

One way how to solve this issue is to **send prepared queries to the server** and request them only by the identifier. Let's say you have this query:

```graphql
{
  leftComparison: hero(episode: EMPIRE) {
    ...comparisonFields
  }
  rightComparison: hero(episode: JEDI) {
    ...comparisonFields
  }
}

fragment comparisonFields on Character {
  name
  appearsIn
  friends {
    name
  }
}

# + additional 20 kB
```

Instead of bundling this query into client code you can persist it to the server and alias it using equivalent ID:

```json
{
  "id": "8b84b5eeceae334fedf369ac6dd0c60a"
}
```

As you can see the request is much smaller and because server already knows what query is behind this ID it can respond with the same subgraph and again - save transferred data.

But this is not the only motivation. Again - practical example but this time from Twitter. They use persistent (static) queries but not only to lower amount of transferred data but also **to make it more secure**. Because you (your company) are the only entity allowed to call this API, you can easily persist these optimized queries and allow only know queries to be called. This is very nice benefit! No one will call very expensive query on your services. Only you can destroy your own servers this way! :)

# How to implement it?
Relay Modern contains compiler to prepare your queries on the client side. It compiles them to the `__generated__` folders next to your components. So you can take advantage of it. Quite a [naive approach](https://github.com/staylor/graphql-wordpress/blob/master/packages/relay-wordpress/tools/persistedQueries.js) would be to find these files (queries) and upload them to your server. In my case, I wrote custom Relay compiler (for other reasons) so I can extend its behavior.

Part of the code generation is `RelayFileWriter`. This class has one hidden configuration option called `persistQuery`. You can pass there your custom function and persist your queries to the backend service (pseudo-code):

```js
const persistQuery = async (queryText: string): Promise<string> => {
  const queryIdentifier = await this.databaseService.persistQuery(queryText);
  return queryIdentifier;
}
```

At this moment Relay will do a very clever thing. It will remove `text` property from the generated query and add your new `id` instead. So all you need to do now is to adjust fetch function for your queries (IDs respectively because you don't have queries anymore). This is [great example](https://github.com/staylor/graphql-wordpress/blob/master/packages/relay-wordpress/src/relay/fetcher.js) how to do it.

This process can be easily done in your CI environment so you don't have to care about it too much. You just write code as usual and everything else is completely automatic. And you can love GraphQL again... :)
