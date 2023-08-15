#!/usr/bin/env bash

EXIT_CODE=0
for depfile in $(find . -type f -not -path "./tests/*" -iname "*deptrac.yaml" | sort)
do
  printf "\nAnalysing ${depfile}\n"
  if [ "$1" == "json" ]; then
    name=$(echo ${depfile} | sed -e "s/\\//_/g")
    name=$(echo ${name} | sed -e "s/\_deptrac.yaml//g")
    vendor/bin/deptrac analyze --no-progress --config-file="$depfile" --formatter=codeclimate --output=var/log/deptrac-${name}.json
    EXIT_CODE=$(($EXIT_CODE+$?))
    cat var/log/deptrac/deptrac-${name}.json
  else
    vendor/bin/deptrac analyze --no-progress --config-file="$depfile"
    EXIT_CODE=$(($EXIT_CODE+$?))
  fi
done

exit $EXIT_CODE
