<?php
$base_path = dirname(__DIR__);
require_once("$base_path/config.php");
require_once("schema.php");
require_once("database.php");

try {
    if(!array_key_exists("time", $_POST) || !array_key_exists("schema", $_POST)) {
        error_log("Received invalid post request, missing time or schema key.");
        die();
    }

    $requestSchema = null;
    foreach($schemas as $schema) {
        if($schema->name == $_POST["schema"]) {
            $requestSchema = $schema;
        }
    }
    if(!$requestSchema) {
        error_log("Unable to find schema specified in request, aborting.");
        die();
    }

    foreach($requestSchema->fields as $field) {
        if(!array_key_exists($field->id, $_POST)) {
            error_log("Missing field " . $field->id . " in request data. Aborting.");
            die();
        }
    }


    $database = Database::createAndConnect();
    $database->addMainEntry($_POST["time"], $requestSchema->getMySQLTableName());
    //$database->addSchemaEntry($requestSchema, $_POST);

} catch(PDOException $e) {
    error_log($e->getMessage());
    print("MySQL Database error. See PHP error log");
    die();
} catch(Error $e) {
    error_log("Hello!");
}