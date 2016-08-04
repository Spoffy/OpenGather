<?php
$basePath = dirname(dirname(__DIR__));
require_once("$basePath/site/schema.php");

$schemaToWebFormat = function ($schema) {
  return $schema->toJSONEncodableWebFormat();
};
$preparedSchemas = array_map($schemaToWebFormat, $schemas);
$json = json_encode($preparedSchemas);

//This file just returns the schemas in JSON
header("Content-Type: application/json");
print($json);
