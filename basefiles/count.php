<?php
// Connection
$manager = new MongoDB\Driver\Manager("***REMOVED***");

// Command
$cmd = new MongoDB\Driver\Command(["count" => "hashlists", "query" => []]);

// Result
$result = $manager->executeCommand('***REMOVED***', $cmd);

// Get Total Online In 1 Hour Ago
var_dump($result);
echo "
-----------------------------
";
var_dump($result->toArray());
echo "
-----------------------------
";
var_dump(json_encode($result->toArray()));
echo "
-----------------------------
";
var_dump(json_decode($result->toArray()));
echo "
-----------------------------
";
var_dump($result->toArray()[0]);
echo "
-----------------------------
";
var_dump($result->toArray()[0]['n']);
echo "
-----------------------------
";
echo $result->toArray()[0]['n'];