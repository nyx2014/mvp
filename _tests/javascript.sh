#!/bin/sh
# Lints all javascript files with standard
# Requires nvm installed

set -e

npm install standard

./node_modules/.bin/standard scripts/**/*.js
