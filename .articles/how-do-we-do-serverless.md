---
id: 2151f134-7f0e-43c0-8382-eab9d336b7ee
timestamp: 1500731161000
title: How do we do serverless?
slug: how-do-we-do-serverless
---
In past few months, I moved completely from PHP backend to the JavaScript semi-frontend (not really backend but definitely not frontend - somewhere between). And I am glad I did it because finally, I can try every cool technology I always wanted but haven't had the opportunity. And one of these technologies is a serverless approach. It basically means that you still have servers but you are not taking care of the bare metal but you'll rather get computational power with all the fancy stuff like auto-scaling, high availability, related services orchestration and so on.

Here at Kiwi.com (Prague), we are working on a lot of projects but one of them is more often pronounced - [the chatbot](http://www.czechcrunch.cz/2017/05/brnenske-kiwi-com-otevira-v-praze-novou-pobocku-se-zamerenim-na-umelou-inteligenci/). This chatbot should help our customer support to manage tasks more easily. But the interesting part is that this application is **completely serverless**. This is how it works.

# It's just a function

The whole chatbot is just a function. Quite long and complicated one though. It's written in Node.js (ECMAScript 2017 - ES8 with a shit-ton of transpilers) and it runs on AWS Lambda. One of the best parts of the serverless is its deployment. We are using [Serverless Framework](https://serverless.com/) and that means that deployment to the serverless infrastructure is as easy as writing `serverless deploy`:

```
$ serverless deploy --stage staging
Serverless: Packaging service...
Serverless: Uploading CloudFormation file to S3...
Serverless: Uploading artifacts...
Serverless: Uploading service .zip file to S3...
Serverless: Validating template...
Serverless: Updating Stack...
Serverless: Checking Stack update progress...
..........
Serverless: Stack update finished...
Service Information
service: chatbot
stage: staging
region: eu-west-1
api keys:
  None
endpoints:
  POST - https://secret.eu-west-1.amazonaws.com/staging/
functions:
  chatbot: chatbot-staging-chatbot
Serverless: Removing old service versions...
```

What does this framework do? Firstly it creates a ZIP file with the already transpiled and tested code. It also creates [AWS CloudFormation](https://aws.amazon.com/cloudformation/) template and uploads it to the AWS S3 storage. Now the magic happens. The new environment is created/updated thanks to the CloudFormation template. In our case, it means that it creates API Gateways, Lambda functions, and DynamoDB tables. This way you can invoke Lambda function just by calling URL address.

The interesting part is that you can extend CloudFormation template however you want. In your case, we are just creating DynamoDB tables but I think you can do whatever you want (means whatever CloudFormation is able to do).

# Dazzle me

I'll show you just a simplified serverless definition (without the DynamoDB because it's too long), but the overall picture should be clear. The whole serverless infrastructure is defined in `serverless.yml`:

```neon
service: chatbot

provider:
  name: aws
  runtime: nodejs6.10
  region: eu-west-1 # Ireland

functions:
  chatbot:
    handler: dist/basicLambda.handler
    events:
      - http:
          path: /
          method: POST
          cors: true
          integration: lambda-proxy
```

It's quite self-explanatory. It's like a scenario: just create AWS Lambda function from `dist/basicLambda.js` file and on `POST` invoke a function in that file called `handler` (and yeah, also support CORS please). Since we are executing only transpiled and tested code it's a good idea to upload just that file:

```neon
package:
  exclude:
    - ./**
  include:
    - dist/*
```

DynamoDB and other services can be defined in the `resources` section. You just have to make sure that you'll setup IAM role permissions as well.

And that's it. This is your "ready for production" environment. The last thing you have to do is just write the Lambda function for the chatbot. Easy...

# Chatbot AWS Lambda function

Since we are using so called `lambda-proxy` integration you have to take care of the inputs and outputs in your code on your own. But it's recommended and you should definitely do that.

```javascript
export async function handler(
  event: Object,
  context: ?Object,
  callback: (error: null, success: Object) => void,
) {
  // error handling and so on...

  callback(null, {
    statusCode: 200,
    headers: {
      'Access-Control-Allow-Origin': '*', // manual CORS (because of lambda-proxy)
    },
    body: JSON.stringify({
      response: "Sorry, I didn't understand your question. Say that again?",
    }),
  });
}
```

Now, for me, this was always so confusing. It's because you hear - just run the function on AWS Lambda. But what does it mean? You have your shiny program full of classes and files and not only one function to run. Well, this is why you have Webpack right? You just inline everything into one file with all the mangling and uglifying and you are almost ready. The last thing is to expose your program via one handler and for AWS Lambda it should be in form of CommonJS (Webpack fragment):

```javascript
output: {
  path: path.resolve(appDirectory, 'dist'),
  filename: '[name].js',
  libraryTarget: "commonjs2",
},
```

And that's it. This is how we do serverless (not only for chatbot). You should definitely try it if you are courageous enough.