<?php
$base_path = dirname(__DIR__);
require_once("$base_path/site/schema.php");
require_once("$base_path/site/database.php");

//TODO Make this functional/neater
//TODO Made this safer
$queryParams = array();
parse_str($_SERVER['QUERY_STRING'], $queryParams);

$result = null;
foreach($schemas as $schema) {
    if(strtolower($schema->id) == strtolower($queryParams["id"])) {
        $result = $schema;
    }
}

$database = Database::createAndConnect();
print(json_encode($database->retrieveData($result)));