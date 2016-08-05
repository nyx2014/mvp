#!/bin/sh
# Runs php -l on all php files

set -e

find . -name "*.php" -print0 | xargs -0 -n1 -P8 php -l
