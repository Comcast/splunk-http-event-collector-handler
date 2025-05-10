<?php

return [
    # Enables Splunk HEC Log Handling
    'enabled' => env('SPLUNK_HEC_ENABLED', true),

    # The Splunk HEC host and port.:
    'host' => env('SPLUNK_HEC_HOST', 'localhost:8088'),

    # The auth token to use for authentication
    'auth_token' => env('SPLUNK_HEC_TOKEN', null),

    # The index to send events to
    'index' => env('SPLUNK_HEC_INDEX', null),

    # The sourcetype to send data to.
    'sourcetype' => env('SPLUNK_HEC_SOURCETYPE', null),

    # Tell Splunk to Acknowledge when submitted data is indexed.
    # NOTE: This must be enabled for your token by your Splunk admins!
    # Not implemented yet
    'use_indexing_acknowledgement' => env('SPLUNK_HEC_USE_INDEXING_ACKNOWLEDGEMENT', false), // NOTE: Not yet implemented

     # Each client should have it's own channel id to use with indexing
     # acknowledgement. Will be used to add new events to the channel
     # and getting the status of previous events so they can be retried
    'channel_id' => env('SPLUNK_HEC_CHANNEL_ID', null), // NOTE: Not yet implemented

    # Whether to verify TLS or not. Not implemented yet.
    'verify_tls' => env('SPLUNK_HEC_VERIFY_TLS', true),

    # The number of seconds to wait before trying again
    'timeout' => (int) env('SPLUNK_HEC_TIMEOUT', 5),

    # The maximum number of tries to send event to Splunk
    'max_tries' => (int) env('SPLUNK_HEC_MAX_TRIES'),

    # The message to log in case we can't send to Splunk HEC
    'emergency_message' => env('SPLUNK_HEC_EMERGENCY_MESSAGE', 'An error occurred while sending logs to Splunk HEC. Please review immediately!'),
    'emergency_channel' => env('SPLUNK_HEC_EMERGENCY_CHANNEL', 'single'),
];
