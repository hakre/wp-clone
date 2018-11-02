#!/bin/bash
#
# bash tests
#
set -euo pipefail
IFS=$'\n\t'


function many_local_vars() {
  local var1 var2
  var2=ok
  echo "${var2-fail}"
  var2=fail
}

many_local_vars
echo ${var2-ok}
