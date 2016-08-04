<?php
$base_path = dirname(__DIR__);
require_once("$base_path/config.php");

//Contains all the query constants.
//This is done so we don't need "global" every time we use one.
//TODO Make all of these constants
class DBQueries
{
    public static $createDatabase = "CREATE DATABASE IF NOT EXISTS " . CONFIG_MYSQL_DB;
    public static $createTables = <<< DB
CREATE TABLE IF NOT EXISTS `app_data` ( 
`time` BIGINT NOT NULL , 
`label` TEXT NOT NULL , 
`type` TEXT NOT NULL , 
`latitude` DOUBLE NOT NULL , 
`longitude` DOUBLE NOT NULL , 
`accuracy` FLOAT NOT NULL )
 ENGINE = CSV;
DB;
    public static $addDataQuery = "INSERT INTO open_data.app_data VALUES (:time, :label, :type, :lat, :long, :accuracy)";
}

class Database {
    public $conn;

    public function connect() {
        $this->conn = new PDO("mysql:host=" . CONFIG_MYSQL_HOST . ";dbname=" . CONFIG_MYSQL_DB,
            CONFIG_MYSQL_USERNAME,
            CONFIG_MYSQL_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->conn->exec(DBQueries::$createDatabase);
        $this->conn->exec(DBQueries::$createTables);

        return $this;
    }

    public static function createAndConnect() {
        $database = new Database();
        $database->connect();
        return $database;
    }

    public function addData($params) {
        $statement = $this->conn->prepare(DBQueries::$addDataQuery);
        print("Adding data");
        $statement->execute($params);
    }
}