I mean not all of the occurrences but at least in GraphQL fields. This is exactly what we did in [Kiwi.com](https://www.kiwi.com/us/) while working on GraphQL API.

This idea didn't come out of nowhere. Actually, I've been thinking about it for a while but after [GraphQL-Europe](https://graphql-europe.org/) I have been convinced (unfortunately I haven't been there personally). But you may ask why? Why would I do that?

There are several reasons for it. Firstly it's prepared for the future (in terms of backward compatibility). This means that if you want to make field non-nullable you can do it anytime you want. But you cannot do the same thing vice versa because it's huge BC break for consumers of your API.

But more importantly, it has a more practical reason. As you probably know the graph in GraphQL consists of a lot of complicated paths and leaves. And every leaf can have different resolver function. In extreme situation, each resolver may be some kind of microservice and this microservice may fail unexpectedly. It's very wrong to let the whole node of the graph fail. And that's exactly what happens if you don't satisfy the non-null rule.

For example, let's run this query:

```graphql
{
  allLocations(term: "PRG") {
    edges {
      node {
        locationId
        name
      }
      cursor
    }
  }
}
```

This query returns an array of all locations related to the search term sorted starting with the best result:

```javascript
{
  "data": {
    "allLocations": {
      "edges": [
        {
          "node": {
            "type": "airport",
            "name": "Václav Havel Airport Prague"
          },
          "cursor": "YXJyYXljb25uZWN0aW9uOjA="
        },
        {
          "node": {
            "type": "city",
            "name": "Prague"
          },
          "cursor": "YXJyYXljb25uZWN0aW9uOjE="
        },
        ...
      ]
    }
  }
}
```

Now let's say that the field `name` is required (means non-nullable). Therefore server MUST return this field. But what happens if resolver for this field for some reason fails?

```javascript
{
  "data": {
    "allLocations": {
      "edges": [
        {
          "node": null,
          "cursor": "YXJyYXljb25uZWN0aW9uOjA="
        },
        {
          "node": null,
          "cursor": "YXJyYXljb25uZWN0aW9uOjE="
        }
      ]
    }
  },
  "errors": [
    {
      "message": "Cannot return null for non-nullable field Location.name.",
      "locations": ...,
      "path": ...
    },
    ...
  ]
}
```

This is not very nice. Especially if we know that the `type` field did not fail. This is why we prefer to write all fields as nullable. The result is much better in this case:


```javascript
{
  "data": {
    "allLocations": {
      "edges": [
        {
          "node": {
            "type": "airport",
            "name": null
          },
          "cursor": "YXJyYXljb25uZWN0aW9uOjA="
        },
        {
          "node": {
            "type": "city",
            "name": null
          },
          "cursor": "YXJyYXljb25uZWN0aW9uOjE="
        }
      ]
    }
  }
}
```

Even though it failed we still got as much as possible in the response. And that's way better. It's a responsibility of the customer (means implementer) to take into account that every field is nullable while implementing GraphQL API.

It worth to mention that there are very good use cases of `GraphQLNonNull`. For example, you want to make fields required (non-nullable) for input arguments and for `GraphQLInputObjectType`. So from this point of view, the title of this article should be "Get rid of GraphQLNonNull in output types"... :)

If you want to know more about this topic, I recommend you to read this thread on GitHub: https://github.com/facebook/graphql/issues/63. There is a lot of good arguments directly from creators of GraphQL and Facebook engineers.

After converting your mind to this new approach it should be quite easy to write eslint rule for this (or whatever lint are you using). So far it really worth it.