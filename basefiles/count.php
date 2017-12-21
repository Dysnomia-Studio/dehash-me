<?php
// Connection
$manager = new MongoDB\Driver\Manager("***REMOVED***");

// Command
$cmd = new MongoDB\Driver\Command(["count" => "hashlists", "query" => []]);

// Result
$result = $manager->executeCommand('***REMOVED***', $cmd);

// Get Total Online In 1 Hour Ago
var_dump($result);
var_dump($result->toArray());
var_dump($result->toArray()['n']);
echo $result->toArray()['n'];