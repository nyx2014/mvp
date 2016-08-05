#!/bin/sh
# Runs csscomb on all css files
# Requires nvm installed and project as git directory

set -e

nvm install node
npm install csscomb

./node_modules/.bin/csscomb styles/*.css

if ! git diff styles/ --quiet; then
    git diff styles/
    echo "CSS linting detected an error. Please use csscomb and resubmit"
    exit 1;
fi
