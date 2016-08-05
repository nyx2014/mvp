#!/bin/sh
# Runs php -l on all php files

set -e

echo "###################"
echo "Startin PHP linting"
echo "###################"

find . -name "*.php" -print0 | xargs -0 -n1 -P8 php -l

echo "#####################"
echo "PHP linting complete!"
echo "#####################"
