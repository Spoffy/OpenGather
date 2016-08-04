<?php
//Defines a schema for the data to be gathered, and the tools to work with that schema.

abstract class Field {
    public $id;
    public $required;

    public function __construct($id, $required=false) {
        $this->id = $id;
        $this->required = $required;
    }

    public abstract function buildFormField();
    public abstract function buildMySQLColumn();

    protected function nullAttributeMySQL() {
        return ($this->required)? "NOT NULL" : "NULL";
    }
}

class TextField extends Field {
    public function buildFormField()
    {
        // TODO: Implement buildFormField() method.
    }

    public function buildMySQLColumn()
    {
        return "`$this->id` TEXT ".$this->nullAttributeMySQL();
    }
}

class ObjectSchema {
    public $name;
    public $fields = array();

    public function __construct($name, $fields = array())
    {
        $this->name = $name;
        $this->fields = $fields;
    }
}

//TODO move this to database.php
function mySQLTableFromSchema($schema) {
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `$schema->name` (";
    //Start the column list
    $createTableQuery .= "id INT NOT NULL AUTO_INCREMENT,";
    foreach($schema->fields as $field) {
        $createTableQuery .= $field->buildMySQLColumn();
        $createTableQuery .= ",";
    }
    $createTableQuery .= "PRIMARY KEY(id)";
    $createTableQuery .= ");";
    //End column list and QUERY
    return $createTableQuery;
}

$testField = new TextField("TextField");
$testSchema = new ObjectSchema("SampleSchema", array($testField));

print(mySQLTableFromSchema($testSchema));
/*
 *  <<< DB
CREATE TABLE IF NOT EXISTS `app_data` (
`time` BIGINT NOT NULL ,
`label` TEXT NOT NULL ,
`type` TEXT NOT NULL ,
`latitude` DOUBLE NOT NULL ,
`longitude` DOUBLE NOT NULL ,
`accuracy` FLOAT NOT NULL )
 ENGINE = CSV;
DB;
 */


