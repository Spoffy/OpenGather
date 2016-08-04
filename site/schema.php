<?php
//Defines a schema for the data to be gathered, and the tools to work with that schema.

abstract class Field {
    public $name;
    public $id;
    public $required;

    public function __construct($name, $id, $required=false) {
        $this->name = $name;
        $this->id = $id;
        $this->required = $required;
    }

    public abstract function buildFormField();
    public abstract function buildMySQLColumn();

    protected $formFieldClasses = "form-field";

    protected function nullAttributeMySQL() {
        return ($this->required)? "NOT NULL" : "NULL";
    }
}

class TextField extends Field {
    public function buildFormField()
    {
        $label = "<label for='form_$this->id'>$this->name</label>";
        $field = "<input id='form_$this->id' class='$this->formFieldClasses' type='text'/>";
        return $label . $field;
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

$testField = new TextField("Testing Field", "testField");
$testSchema = new ObjectSchema("SampleSchema", array($testField));
$schema = $testSchema;


