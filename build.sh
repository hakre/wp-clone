#!/bin/bash
#
# local build of the project
#
# usage: ./build.sh
#
set -euo pipefail
IFS=$'\n\t'


# run shell checks
shellcheck -x bin/wp-clone

# fix spaces at end of line in md
sed -i -e 's/  *$//' README.md
