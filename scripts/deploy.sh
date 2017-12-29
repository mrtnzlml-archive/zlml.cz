#!/usr/bin/env bash

# see: https://www.gnu.org/software/bash/manual/html_node/The-Set-Builtin.html
set -e # exit immediately if a pipeline returns a non-zero status

yarn install --production
serverless config credentials --provider aws --key $AWS_ACCESS_KEY_ID --secret $AWS_SECRET_ACCESS_KEY
serverless deploy --verbose
curl -s --head  --request GET https://zlml.cz | grep "200 OK" > /dev/null || exit 1
