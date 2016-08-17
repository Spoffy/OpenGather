<?php
$base_path = dirname(__DIR__);
require_once("$base_path/src/schema.php");
require_once("$base_path/config.php");

//Contains all the query constants.
//This is done so we don't need "global" every time we use one.
//TODO Make all of these constants
class DBQueries
{
    public static $createDatabase;
    public static $createMainTable = <<< DB
CREATE TABLE IF NOT EXISTS `data_entries` ( 
`id` INT NOT NULL AUTO_INCREMENT,
`time` BIGINT NOT NULL , 
`schema` TEXT NOT NULL,
PRIMARY KEY(`id`)
);
DB;
    public static $addMainEntryQuery = "INSERT INTO data_entries (`time`, `schema`) VALUES (:time, :schema)";
}
//Down here as PHP 5.5 or less doesn't support expressions as initializers.
DBQueries::$createDatabase = "CREATE DATABASE IF NOT EXISTS " . CONFIG_MYSQL_DB;

class Database {
    public $conn;

    public function connect() {
        $this->conn = new PDO("mysql:host=" . CONFIG_MYSQL_HOST . ";dbname=" . CONFIG_MYSQL_DB,
            CONFIG_MYSQL_USERNAME,
            CONFIG_MYSQL_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //TODO find a way to not run this EVERY connection
        $this->conn->exec(DBQueries::$createDatabase);
        $this->conn->exec(DBQueries::$createMainTable);

        global $schemas;
        foreach($schemas as $schema) {
            $this->conn->exec($schema->buildMySQLCreateTable());
        }

        return $this;
    }

    public static function createAndConnect() {
        $database = new Database();
        $database->connect();
        return $database;
    }

    public function addEntry($time, $schema, $data) {
        $mainStatement = $this->conn->prepare(DBQueries::$addMainEntryQuery);
        $mainStatement->execute(array(":time" => $time, ":schema" => $schema->id));
        $insertionBindings = array();
        foreach($schema->fields as $field) {
            $insertionBindings[":$field->id"] = $data[$field->id];
        }
        $insertStatement = $this->conn->prepare($schema->buildMySQLInsertQuery());
        $insertStatement->execute($insertionBindings);
    }

    public function retrieveData($schema) {
        $statement = $this->conn->query($schema->buildMySQLSelectQuery());
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}