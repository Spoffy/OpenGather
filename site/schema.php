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

class DropdownField extends Field {
    public function buildFormField()
    {
        // TODO: Implement buildFormField() method.
    }

    public function buildMySQLColumn()
    {
        // TODO: Implement buildMySQLColumn() method.
    }
}

//TODO Add CSS highlighting to readonly fields
class GeoField extends Field {
    public function buildFormField()
    {
        $label = "<label for='".$this->getFormFieldId()."'>$this->name</label>";
        $field = "<input disabled id='".$this->getFormFieldId()."' class='$this->formFieldClasses' type='text' value='0' />";
        return $label . $field;
    }

    public function buildMySQLColumn()
    {
        return "`$this->id` DECIMAL(13,10) ".$this->nullAttributeMySQL();
    }
}

class ObjectSchema {
    public $name;
    public $id;
    public $fields = array();

    public function __construct($name, $fields = array())
    {
        $this->name = $name;
        $this->id = $name;
        $this->fields = $fields;
    }

    public function buildMySQLCreateTable() {
        $createTableQuery = "CREATE TABLE IF NOT EXISTS `".$this->id."` (";
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

    public function buildMySQLInsertQuery() {
        //TODO Find a way to move LAST_INSERT_ID() to the database module.
        //It introduces an ordering requirement that's bad.
        $insertQuery = "INSERT INTO `".$this->id."` VALUES (LAST_INSERT_ID()";
        foreach($this->fields as $field) {
            $insertQuery .= ", :$field->id";
        }
        $insertQuery .= ");";
        return $insertQuery;
    }

    //TODO Make posting + form use schema id, rather than name.
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

//TODO Make JS validate required fields.
$entranceSchema = new ObjectSchema("Building Entrance", array(
    new TextField("Building Number", "buildingId", false),
    new TextField("Entrance Label", "entranceId", false),
    new TextField("Description", "description", false),
    new GeoField("Latitude", "lat", false),
    new GeoField("Longitude", "long", false),
    new TextField("Access Method Daytime", "accessDaytime", false),
    new TextField("Access Method Evening", "accessEvening", false)
));

$otherSchema = new ObjectSchema("Other", array(
    new TextField("Description", "description", false)
));

$schemas = array($entranceSchema, $otherSchema);