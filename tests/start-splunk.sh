#!/bin/bash
set -e

TOKEN_WITHOUT_ACK=$(uuidgen)
TOKEN_WITH_ACK=$(uuidgen)
PASSWORD=$(uuidgen)

echo "# Setting Splunk config"
cp tests/config/inputs.conf.stub tests/config/inputs.conf;
sed -i'' "s/{{SPLUNK_HEC_TOKEN_WITHOUT_ACK}}/${TOKEN_WITHOUT_ACK}/g" tests/config/inputs.conf
sed -i'' "s/{{SPLUNK_HEC_TOKEN_WITH_ACK}}/${TOKEN_WITH_ACK}/g" tests/config/inputs.conf
sed -i'' "s/{{SPLUNK_HEC_HOST}}/localhost/g" tests/config/inputs.conf
touch .env

printf $TOKEN_WITHOUT_ACK > tests/.token_without_ack
printf $TOKEN_WITH_ACK > tests/.token_with_ack
printf $PASSWORD > tests/.password

echo "# Splunk UI Username is 'admin' and Password is '${PASSWORD}'."

echo "# Splunk HTTP Event Collector Auth Token without indexing acknowledgement is '${TOKEN_WITHOUT_ACK}'."

echo "# Splunk HTTP Event Collector Auth Token with indexing acknowledgement is '${TOKEN_WITH_ACK}'."

echo "# Index is 'main' and default Sourcetype is 'main'"

echo "# If opening Splunk UI on https://localhost:8000, type 'thisisunsafe'"
echo "# when focused on the browser's 'CERT_INVALID' page."
echo "# (https://dblazeski.medium.com/chrome-bypass-net-err-cert-invalid-for-development-daefae43eb12)"

CONTAINER=$(docker run -it --name splunk-lab --rm -d -e SPLUNK_START_ARGS="--accept-license" -e SPLUNK_PASSWORD="${PASSWORD}" -p 8000:8000 -p 8088:8088 -p 8089:8089 -v $(pwd)/tests/config:/opt/splunk/etc/apps/splunk-lab/local dmuth1/splunk-lab)

echo "# Waiting for web server to become healthy..."
sleep 7

echo "# Splunk Lab started with container ID $CONTAINER#"