<?php
$basePath = dirname(dirname(__DIR__));
require_once("$basePath/site/schema.php");

//This file just returns the schema in JSON
header("Content-Type: application/json");
print($schema->toJSON());