---
timestamp: 1509494028280
title: GraphQL Summit 2017 - San Francisco
slug: graphql-summit-2017-san-francisco
---

GraphQL Summit was really a great eye-opener. I never thought about GraphQL in this way and I never realized how is the company I am working for falling behind with this progressive technology. You will have the opportunity to watch all talks on [Apollo GraphQL YouTube channel](https://www.youtube.com/channel/UC0pEW_GOrMJ23l8QcrGdKSw/feed) or you can read quite comprehensive reviews of each GraphQL talk [on this blog](https://about.sourcegraph.com/graphql/). But what you cannot is to feel the atmosphere of the summit and you cannot talk with GraphQL creators from Facebook to discover how to use it properly. So I will try to give you sneak peek preview of the feature... :)

# Forget simple stupid serverless

I **love** serverless technology. But sometimes it's driving me crazy because there is a lot of edge cases when being serverless is actually a big limitation. And it's not going to be better in the future. Sure, you can be still serverless but the new trend is to build some kind of proxy (using Docker for example) for your GraphQL API. And it makes sense. One of the first projects using this strategy is [Apollo Engine](https://www.apollographql.com/engine/) (replacement for deprecated Apollo Optics). Apollo Engine behaves really like a caching proxy for your API so you can:

1. measure performance of your queries and fields of types
2. log all errors in GraphQL responses
3. return cached responses without even invoking your GraphQL backend
4. theoretically make persistent connections (WS) and measure the performance of your subscriptions

The last point is not real yet. But it may be very real in the future. Especially the possibility to make persistent connections for subscriptions is very important (one of the serverless architecture limitations). The measurement is performed by extending the GraphQL response with additional metadata defined in this [unofficial specification](https://github.com/apollographql/apollo-tracing) (still fully compatible with official specification). You can similarly control the [cache expiration](https://github.com/apollographql/apollo-cache-control) in the proxy. I like it, it's clever but no longer completely serverless! On the other hand, even Serverless Framework is not going to be completely "serverless" (see [serverless/event-gateway](https://github.com/serverless/event-gateway)). It has very similar design characteristics and I am kinda glad that the trend is not to force serverless technology everywhere.

# Wanna go fast? Go alone. Wanna go far? Go together...

This is how I would describe the new trend of so-called "schema stitching". I saw it so many times. Unfortunately, I cannot show you real examples because our engineers (frontend and backend) don't care about GraphQL and we are miles from such architectural designs so let me at least explain it theoretically. There is a lot of companies with a lot of microservices (IBM, Air France, Twitter, Facebook). And for example in IBM each team is responsible for each part of the GraphQL Schema. And we are talking about 30+ teams with completely different codebases. Now here is the challenge: if you are building frontend for such an application you cannot really call multiple REST endpoints and handle all the errors and weirdnesses of each individual API (well, we are doing it but it's mess and I do not recommend it). Instead, you can build one API and work with assumptions.

This is a great use case for schema stitching. Just build GraphQL schema for each microservice (using [GrAMPS](https://gramps-graphql.github.io/gramps-express/) for example - GraphQL Apollo Microservice Patterns Server) and connect them using schema stitching. Or leave it to GrAMPS as is. The result is one big GraphQL Schema with standardized behavior, introspection and everything that GraphQL offers.

To be fair - Air France doesn't use this approach. It's because their architecture is a little bit different (4 independent applications in one application) and one proxy for schema stitching is considered a single point of failure. So it's not really silver bullet but it makes sense for most applications (or you think that IBM is small?) and even Air France could build it like IBM. From what I understand their decision was based solely on the scare.

# Some errors are not errors at all

One of the most often asked questions while building GraphQL API for frontend is: "How do we handle form and application errors?" And one of the first designs from newbies would be to extend `errors` field in the response. But this is not very flexible because suddenly you introduced tight coupling with UI (e.g. what field errored) and even though it's true that "your API is a user interface" - you should not take this literally.

Instead what about thinking about errors little bit differently. What if some errors are not errors at all? What if **errors are actually data**? What if you could provide all necessary information in the response like this?

```graphql
mutation createUser {
  createUser(email: "test@example.com") {
    user { id, email }
    wasSuccessful
    userErrors {
      fields, message
    }
  }
}
```

Think about it. [Read about it](https://about.sourcegraph.com/graphql/what-went-wrong-best-practices-for-surfacing-error-messages-in-a-graphql-api/). Be open-minded... :)

# GraphQL is starting to be weird

The GraphQL specification is very benevolent. And that's great. But it also means that you can do really weird stuff with it. For example:

```graphql
query GetUserData($email: String!) {
  user(email: $email) @rest(route: "/users/email/:email", email: $email) {
    id, firstName
    deviceType @client
    friends @rest(route: "/users/friends/:id") @provides(vars: ["id"]) {
      firstName
    }
    hobbies @sql(query: "SELECT * FROM hobbies WHERE user_id = ?", variables: ...) {
      name
    }
  }
}
```

I made up the SQL part but I saw something similar anyway. And why not - you can do it. But why? Especially the `@rest` parts are examples from Apollo Client 2.0. And I am always asking - is it so difficult to write simple resolver with `fetch` function? GraphQL syntax is very powerful but it doesn't mean that we must use all the power, right?

# Last thoughts

GraphQL is enabling technology. It enables you to move forward much faster in a cleaner way. A great example is the evolution of GraphQL itself. Did you know that most of the features were developed on client application and not on the server? It all started with FQL that transformed into GraphQL. Then GraphQL fragments - originally implemented only on the server with "defragmentation" and server implemented fragments after ~3 months of fragments **in production**. Mutations? Implemented half year behind the client (originally were mutations translated to the REST call on the client). Subscriptions? The same story. I love this idea.

And it actually goes much further. GraphQL is not only way how to fetch data. GraphQL is way how to describe your needs and it starts with the graphical designs. Stop building "lorem ipsum" interfaces and start using real data while designing. The "lorem ipsum" is bullshit anyway because it never works as expected. I am waiting for tools like this and I cannot wait what will be the next step in applications development.

## Nuggets of awesomeness (alphabetically)

- [Apollo GraphQL code generator](https://github.com/apollographql/apollo-codegen)
- [Gest - sensible GraphQL testing](https://github.com/mfix22/gest)
- [GRANDstack](http://grandstack.io/)
- [Graphcool Framework](https://github.com/graphcool/framework)
- [Graphene JS](http://graphene-js.org/)
- [GraphQL Code Generator](https://github.com/dotansimha/graphql-code-generator)
- [GraphQL Normalized Types](https://github.com/mfix22/gnt)
- [GraphQL Radio](https://graphqlradio.com/)
- [GraphQL Workshops](http://graphqlworkshops.com/)
- [GraphQLDocs](https://github.com/gjtorikian/graphql-docs) (Ruby)
- [JAMstack](https://jamstack.org/)
- [Quiver - next Generation GraphQL Engine](http://graphql-quiver.com/) (stay tuned)
