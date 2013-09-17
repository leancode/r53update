<?php

$config = array(

    // The AWS key and secret to connect to Route 53
    'aws_key' => '',
    'aws_secret' => '',

    // The HTTP source page for giving out your IP. Must only return your IP, nothing else.
    'ip_source' => 'http://bot.whatismyipaddress.com/',

    // Name of the zone that contains the record to update
    'zone' => 'example.com.',

    // The zone to update with the current IP. Must be an A record.
    'record' => 'dyndns.example.com.',

    // The TTL with which to create the new record
    'ttl' => 300

);
