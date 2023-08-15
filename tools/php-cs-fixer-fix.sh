#!/usr/bin/env bash

set -e

RELATIVE_PATH_TO_SCRIPT_DIR="$(dirname ${BASH_SOURCE[0]})"
SCRIPT_DIR=$(cd "$RELATIVE_PATH_TO_SCRIPT_DIR" && pwd -P)

"$SCRIPT_DIR/php-cs-fixer/vendor/bin/php-cs-fixer" fix --config "$SCRIPT_DIR/php-cs-fixer/.php-cs-fixer.php" -v --using-cache=no $@
