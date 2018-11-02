#!/bin/bash
#
# running some tests around extracting configuration values from wp-config.php
#
set -euo pipefail
IFS=$'\n\t'

readonly wp_path="${1}"

(
  sed '/\/\* That'"'"'s all, stop editing! Happy blogging. \*\//q' "${wp_path}/wp-config.php"
  echo 'print_r(array_intersect_key(get_defined_constants(), array_flip(["DB_NAME"])));'
  echo 'echo "$table_prefix\n";'
) | php

sed -n '/define(/s/\s*//p'  "${wp_path}/wp-config.php" | sed 's/ \+/ /g' | grep -v ABSPATH

(
  sed -n '/define(/s/\s*//p'  "${wp_path}/wp-config.php" | sed 's/ \+/ /g' | grep -v ABSPATH
)

(
  echo "foo"
  >&2 echo "bar"
) 2>&1 >/dev/null

sed -n '/define('"'"'DB_NAME'"'"',/s/\s*//p'  "${wp_path}/wp-config.php" | sed 's/ \+/ /g' | grep -v ABSPATH || true

sed -n "s/.*DB_NAME.*, '\(.*\)');/\1/p" "${wp_path}/wp-config.php"
