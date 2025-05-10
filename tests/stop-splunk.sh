#!/bin/bash

set -e

CONTAINER=$(docker container ls | grep dmuth1/splunk-lab | awk '{print $1}')

printf "\n\n\n############################################\n"
echo "# Stopping Splunk Lab container $CONTAINER #"

docker container stop $CONTAINER > /dev/null 2>&1 || echo "Splunk is not running. Cleaning up temporary test files..."

echo "# Removing Splunk Lab config files          #"
rm tests/config/inputs.conf > /dev/null 2>&1 ||:
rm .env > /dev/null 2>&1 ||:
rm tests/.password > /dev/null 2>&1 ||:
rm tests/.token_without_ack > /dev/null 2>&1 ||:
echo "############################################"