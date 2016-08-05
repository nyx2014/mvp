#!/bin/sh
# Lints all javascript files with standard
# Requires nvm installed

set -e

nvm install node
npm install standard

./node_modules/.bin/standard scripts/**/*.js
