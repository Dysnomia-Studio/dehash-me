<?php
// Connection
$manager = new MongoDB\Driver\Manager("***REMOVED***");

// Command
$cmd = new MongoDB\Driver\Command(["count" => "account", "query" => []]);

// Result
$result = $Manager->executeCommand('***REMOVED***.hashlists', $cmd);

// Get Total Online In 1 Hour Ago
echo count($result->toArray()->n);