<?php

/*
 * CNAMER depends on DNS lookups to perform properly, the server could be up
 * but if the DNS requests are failing... something is wrong. This script will
 * perform a few requests and check if they're working, if they are then hooray
 * if not... uh oh, report that the system is down!
 */

require_once __DIR__ . '/../bootstrap.php';

$requests = array(
    "google" => array(
        "domain" => "wikipedia.cnamer.org",
        "uri" => "",
    ),
    "cnamer" => array(
        "domain" => "cnamer.org",
        "uri" => "",
    ),
    "github" => array(
        "domain" => "github.cnamer.org",
        "uri" => "",
    ),
);

$failures = 0;
$successes = 0;
$status = 'DOWN';

foreach($requests as $name => $request) {
    $cnamer = new Cnamer\Cnamer($request);
    try {
        $cnamer->cache_time(0);
        $redirect = $cnamer->redirect(); // TODO: Don't load from cache!
        $successes++;
    } catch (Exception $e) {
        $failures++;
    }
}

$response_time = round((((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) / 3) * 1000), 3);

if($successes >= 2) { // 
    $status = 'OK';
}

echo "<pingdom_http_custom_check>
<status>{$status}</status>
<response_time>{$response_time}</response_time>
</pingdom_http_custom_check>";