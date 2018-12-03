#!/usr/bin/env bash
set -eo pipefail

if [[ "$(pwd)" == "$(cd "$(dirname "$0")"; pwd -P)" ]]; then
  echo "Can only be executed from project root!"
  exit 1
fi

exec ./build/node_modules/.bin/karma start tests/karma.config.js --single-run
