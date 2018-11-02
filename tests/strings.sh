#!/bin/bash
#
# running tests regarding extracting information from strings
#
set -euo pipefail
IFS=$'\n\t'

readonly source="${1-ssh-host:path/on/remote}"
readonly source_host="$(echo "${source}" | sed 's/:.*//')"
readonly source_path="$(echo "${source}" | sed 's/[^:]*://')"

echo "${source_host}"
echo "${source/:*/}"

echo "${source_path}"
echo "${source/*:/}"
