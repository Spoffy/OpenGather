<?php
$base_path = dirname(__DIR__);
require_once("$base_path/config.php");
require_once("database.php");

try {
    $params = array(
        ":time" => $_POST["time"],
        ":label" => $_POST["label"],
        ":type" => $_POST["type"]
    );

    if($_POST["position"]) {
        $params[":lat"] = $_POST["position"]["lat"];
        $params[":long"] = $_POST["position"]["long"];
        $params[":accuracy"] = $_POST["position"]["accuracy"];
    } else {
        $params[":lat"] = 0;
        $params[":long"] = 0;
        $params[":accuracy"] = -1;
    }

    $database = Database::createAndConnect();
    $database->addData($params);
} catch(PDOException $e) {
    error_log($e->getMessage());
    print("MySQL Database error. See PHP error log");
    die();
} catch(Error $e) {
    error_log("Hello!");
}