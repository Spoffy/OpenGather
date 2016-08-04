<?php
$basePath = dirname(dirname(__DIR__));
require_once("$basePath/site/schema.php");

$preparedSchemas = array();
foreach($schemas as $schema) {
    $preparedSchemas[] = $schema->toJSONEncodableWebFormat();
}
$json = json_encode($preparedSchemas);

//This file just returns the schemas in JSON
header("Content-Type: application/json");
print($json);
