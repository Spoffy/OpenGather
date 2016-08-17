<?php
$base_path = dirname(__DIR__);
require_once("$base_path/site/schema.php");
require_once("$base_path/site/database.php");

//TODO Make this functional/neater
//TODO Made this safer
$queryParams = array();
parse_str($_SERVER['QUERY_STRING'], $queryParams);

$database = Database::createAndConnect();
$output = [];

if(key_exists("id", $queryParams)) {
    $result = null;
    foreach($schemas as $schema) {
        if(strtolower($schema->id) == strtolower($queryParams["id"])) {
            $result = $schema;
        }
    }
    if(!$result) { die(); }
    $output = $database->retrieveData($result);
} else {
    foreach($schemas as $schema) {
        $output = array_merge($output, $database->retrieveData($schema));
    }
}

print(json_encode($output));

