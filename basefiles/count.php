<?php
// Connection
$manager = new MongoDB\Driver\Manager("***REMOVED***");

// Command
$cmd = new MongoDB\Driver\Command(["count" => "hashlists", "query" => []]);

// Result
$result = $manager->executeCommand('***REMOVED***', $cmd);
$results = $result->toArray();

// Get Total Online In 1 Hour Ago
var_dump($result);
echo "
-----------------------------
";
var_dump($results);
echo "
-----------------------------
";
var_dump($results[0]);
echo "
-----------------------------
";
var_dump(json_decode(json_encode($results[0]), true)['n']);
echo "
-----------------------------
";
echo json_decode(json_encode($results[0]), true)['n'];