#!/usr/bin/env bash

set -e

RELATIVE_PATH_TO_SCRIPT_DIR="$(dirname ${BASH_SOURCE[0]})"
SCRIPT_DIR=$(cd "$RELATIVE_PATH_TO_SCRIPT_DIR" && pwd -P)

git checkout master
git fetch
git reset --hard origin/master