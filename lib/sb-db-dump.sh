#!/bin/bash
#
# sandbox database dump (remote)
#
set -euo pipefail
IFS=$'\n\t'

{
  mkdir -p ~/.wp-sandbox

  readonly wp_path="${1}"

  wpc() { sed -n "s/.*${1}.*, '\(.*\)');.*/\1/p" "${wp_path}/wp-config.php" ; }

  readonly db_name="$(wpc DB_NAME)"
  readonly dump_path=~/.wp-sandbox/"${db_name}.sql.gz"

  MYSQL_PWD="$(wpc DB_PASSWORD)" mysqldump --user="$(wpc DB_USER)" --password="$(wpc DB_PASSWORD)" \
    --add-drop-database -h "$(wpc DB_HOST)" "${db_name}" \
     | gzip -9 > "${dump_path}"

  echo "${dump_path}"
}
