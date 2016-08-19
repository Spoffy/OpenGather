<?php
$base_path = dirname(__DIR__);
require_once("$base_path/current_schema.php");
require_once("$base_path/src/database.php");
require_once("$base_path/src/functional/pluck.php");
require_once("$base_path/src/functional/first.php");

//TODO Make this functional/neater
//TODO Made this safer
$queryParams = array();
parse_str($_SERVER['QUERY_STRING'], $queryParams);

$database = Database::createAndConnect();
$output = array();

//Attempting to select results for only a single schema
if(key_exists("schema", $queryParams)) {
    $querySchemaId = $queryParams["schema"];
    $schemaIdEqualsQueryId = function($schema) {global $querySchemaId; return $schema->id == $querySchemaId;};
    //First schema where the id equals given id.
    $schema = first($schemas, $schemaIdEqualsQueryId);
    if(!$schema) { die(); }
    $convertDataToJSONEncodableArray = array($schema, 'dataToJSONEncodable');
    $output = array_map($convertDataToJSONEncodableArray, $database->retrieveData($schema));
} else {
    foreach($schemas as $schema) {
        $output = array_merge($output, array_map(array($schema, 'convertDataToJSONEncodable'), $database->retrieveData($schema)));
    }
}

print(json_encode($output));

