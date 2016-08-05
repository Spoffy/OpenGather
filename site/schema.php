<?php
//Defines a schemas for the data to be gathered, and the tools to work with that schemas.

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

    protected $formFieldClasses = "form-field";
    public function getFormFieldId() { return "form_$this->id"; }


    public abstract function buildMySQLColumn();

    protected function nullAttributeMySQL() {
        return ($this->required)? "NOT NULL" : "NULL";
    }
}

class TextField extends Field {
    public function buildFormField()
    {
        $label = "<label for='".$this->getFormFieldId()."'>$this->name</label>";
        $field = "<input id='".$this->getFormFieldId()."' class='$this->formFieldClasses' type='text'/>";
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

    public function buildMySQLCreateTable() {
        $createTableQuery = "CREATE TABLE IF NOT EXISTS `$this->name` (";
        //Start the column list
        $createTableQuery .= "id INT NOT NULL AUTO_INCREMENT,";
        foreach($this->fields as $field) {
            $createTableQuery .= $field->buildMySQLColumn();
            $createTableQuery .= ",";
        }
        $createTableQuery .= "PRIMARY KEY(id)";
        $createTableQuery .= ");";
        //End column list and QUERY
        return $createTableQuery;
    }

    public function getMySQLTableName() {
        return $this->name;
    }

    public function toJSONEncodableWebFormat() {
        $object = array(
            "name" => $this->name,
            "fields" => array()
        );
        foreach($this->fields as $field) {
            $object["fields"][] = array(
                "name" => $field->name,
                "id" => $field->id,
                "formId" => $field->getFormFieldId(),
                "html" => $field->buildFormField()
            );
        }
        return $object;
    }
}

$testField = new TextField("First Schema Field", "schemaField11");
$testSchema = new ObjectSchema("First Schema", array($testField));

$testField2 = new TextField("Second Schema Field", "schemaField21");
$testField3 = new TextField("Second Schema Field 2", "schemaField22");
$testSchema2 = new ObjectSchema("Second Schema", array($testField2));
$schemas = array($testSchema, $testSchema2);