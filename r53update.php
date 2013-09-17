<?php

require 'vendor/autoload.php';

require 'config.php';

use Aws\Route53\Route53Client;

$client = Route53Client::factory(array('key' => $config['aws_key'], 'secret' => $config['aws_secret']));
$zones = $client->listHostedZones()->getAll();
$list = $zones["HostedZones"];

$res = array_filter($list, function($el) use($config) { return ($el["Name"] == $config['zone']); });

if (count($res) != 1)
{
    die("Could not find zone '" . $config['zone'] . "'");
}

$domain = array_shift($res);

// Get current resource record sets
$current_rr = $client->listResourceRecordSets(array('HostedZoneId' => $domain['Id']))->getAll();

// Find the current record set
$old_rrs = array_filter($current_rr['ResourceRecordSets'], function($el)use($config) { return $el['Name'] == $config['record']; });
$old_rr = array_shift($old_rrs);

// Figure out the current IP address
$current_ip = trim(file_get_contents($config['ip_source']));


// Update the I
$result = $client->changeResourceRecordSets(array(
    'HostedZoneId' => $domain['Id'],
    'ChangeBatch' => array(
        'Comment' => 'r53update, https://github.com/nmenglund/r53update',
        'Changes' => array(
            array(
                'Action' => 'DELETE',
                'ResourceRecordSet' => $old_rr),
            array(
                'Action' => 'CREATE',
                'ResourceRecordSet' => array(
                    'Name' => $config['record'],
                    'Type' => 'A',
                    'TTL' => $config['ttl'],
                    'ResourceRecords' => array(
                        array('Value' => $current_ip)
                    ))
                )
            )
        )));
