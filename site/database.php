<?php
$base_path = dirname(__DIR__);
require_once("$base_path/config.php");

//Contains all the query constants.
//This is done so we don't need "global" every time we use one.
//TODO Make all of these constants
class DBQueries
{
    public static $createDatabase = "CREATE DATABASE IF NOT EXISTS " . CONFIG_MYSQL_DB;
    public static $createMainTable = <<< DB
CREATE TABLE IF NOT EXISTS `data_entries` ( 
`id` INT NOT NULL AUTO_INCREMENT,
`time` BIGINT NOT NULL , 
`schema` TEXT NOT NULL,
PRIMARY KEY(`id`)
);
DB;
    public static $addMainEntryQuery = "INSERT INTO open_data.data_entries (`time`, `schema`) VALUES (:time, :schema)";
}

class Database {
    public $conn;

    public function connect() {
        $this->conn = new PDO("mysql:host=" . CONFIG_MYSQL_HOST . ";dbname=" . CONFIG_MYSQL_DB,
            CONFIG_MYSQL_USERNAME,
            CONFIG_MYSQL_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->conn->exec(DBQueries::$createDatabase);
        $this->conn->exec(DBQueries::$createMainTable);

        return $this;
    }

    public static function createAndConnect() {
        $database = new Database();
        $database->connect();
        return $database;
    }

    public function addMainEntry($time, $schema) {
        $statement = $this->conn->prepare(DBQueries::$addMainEntryQuery);
        $statement->execute(array(":time" => $time, ":schema" => $schema));
    }
}