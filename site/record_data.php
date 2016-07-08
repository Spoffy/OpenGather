<?php

$servername = "localhost";
$username = "root";
$password = "";

$createDatabaseQuery = "CREATE DATABASE IF NOT EXISTS open_data";

$createTableQuery = <<< DB
CREATE TABLE IF NOT EXISTS `open_data`.`app_data` ( 
`time` BIGINT NOT NULL , 
`label` TEXT NOT NULL , 
`type` TEXT NOT NULL , 
`latitude` DOUBLE NOT NULL , 
`longitude` DOUBLE NOT NULL , 
`accuracy` FLOAT NOT NULL )
 ENGINE = CSV;
DB;


try {
    $conn = new PDO("mysql:host=$servername;dbname=open_data", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec($createDatabaseQuery);
    $conn->exec($createTableQuery);

    $addDataQuery = "INSERT INTO open_data.app_data VALUES (:time, :label, :type, :lat, :long, :accuracy)";

    $addDataStatement = $conn->prepare($addDataQuery);

    $params = array(
        ":time" => $_POST["time"],
        ":label" => $_POST["label"],
        ":type" => $_POST["type"],
        ":lat" => $_POST["position"]["lat"],
        ":long" => $_POST["position"]["long"],
        ":accuracy" => $_POST["position"]["accuracy"]
    );

    print($addDataStatement->execute($params));
} catch(PDOException $e) {
    error_log($e->getMessage());
    header($_SERVER['SERVER_PROTOCOL'] . '405 Internal Server Error');
    print("MySQL Database error. See PHP error log");
    die();
}
