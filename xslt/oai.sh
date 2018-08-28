#!/bin/bash

BASE=${OAIPMH:-https://lazardb.gbv.de/api/plugin/base/oai/oai}

GetRecordURL () {
    echo "$BASE?verb=GetRecord&identifier=$2&metadataPrefix=$3"
}

IDENTIFIER="$1"
FORMAT="${2:-easydb}"

if [ $# -lt 1 ]; then
    echo 1>&2 "missing identifier to get in format $FORMAT"
    exit 2
fi
