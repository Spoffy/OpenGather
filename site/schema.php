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

//TODO Figure out a way of implementing null drop dropdown fields.
class DropdownField extends Field {
    private $items;

    public function __construct($name, $id, $items=array(), $required=false) {
        parent::__construct($name, $id, $required);
        $this->items = $items;
    }

    public function buildFormField()
    {
        $field = "<label for='" . $this->getFormFieldId() . "'>$this->name</label>";
        $field .= "<select id='" . $this->getFormFieldId(). "' class='".$this->formFieldClasses . "'>\n";
        foreach($this->items as $item) {
            $field .= "<option value='$item'>$item</option>\n";
        }
        $field .= "</select>\n";
        return $field;
    }

    public function buildMySQLColumn()
    {
        $formatted_items = array_map(function ($item) {
            return "'$item'";
        }, $this->items);
        $csv_items = implode(",", $formatted_items);
        return "`$this->id` ENUM(" . $csv_items . ") " . $this->nullAttributeMySQL();
    }
}

//TODO Replace/Add fields for Lat,Long with pre-specified IDs.
class GeoField extends Field {
    public function buildFormField()
    {
        $label = "<label for='".$this->getFormFieldId()."'>$this->name</label>";
        $field = "<input disabled id='".$this->getFormFieldId()."' class='$this->formFieldClasses' type='text' value='0' />";
        return $label . $field;
    }

    public function buildMySQLColumn()
    {
        //DECIMAL(9,6) gives us accuracy to around 0.11m, more than enough.
        return "`$this->id` DECIMAL(9,6) ".$this->nullAttributeMySQL();
    }
}

//TODO Validate and check for identical IDs within schema. Two fields shouldn't be nameable the same.
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

    public function buildMySQLSelectQuery() {
        $columns = ['time'] + array_map(function ($field) { return $field->id; }, $this->fields);
        $quotedColumns = array_map(function($name) { return "`$name`"; }, $columns);
        $columnString = implode($quotedColumns, ",");
        $selectQuery = "SELECT $columnString FROM data_entries INNER JOIN `$this->id` ON `data_entries`.`id` = `$this->id`.`id`;";
        return $selectQuery;
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
$accessOptions = array("Unknown", "Open", "Card Scanner", "Key");
$schemas = array(
    new ObjectSchema("Building Entrance", array(
        new TextField("Building Number", "buildingId", false),
        new TextField("Entrance Label", "entranceId", false),
        new TextField("Description", "description", false),
        new GeoField("Latitude", "lat", false),
        new GeoField("Longitude", "long", false),
        new DropdownField("Access Method Daytime", "accessDaytime", $accessOptions),
        new DropdownField("Access Method Evening", "accessEvening", $accessOptions),
        new DropdownField("Opening Method", "openingMethod", array("Manual - Push/Pull", "Sensor + Automatic", "Pushbutton + Automatic"))
    )),

    new ObjectSchema("Image (Building)", array(
        new TextField("Building Number", "buildingId", false),
        new GeoField("Latitude", "lat", false),
        new GeoField("Longitude", "long", false)
    )),

    new ObjectSchema("Drinking Water Source", array(
        new TextField("Building Number", "buildingId", false),
        new TextField("Floor", "floor", false),
        new GeoField("Latitude", "lat", false),
        new GeoField("Longitude", "long", false)
    )),

    new ObjectSchema("Public Showers", array(
        new TextField("Building Number", "buildingId", false),
        new TextField("Floor", "floor", false),
        new TextField("Room Number", "roomId", false),
        new GeoField("Latitude", "lat", false),
        new GeoField("Longitude", "long", false)
    )),

    new ObjectSchema("Point of Service", array(
        new TextField("Description", "description", false),
        new TextField("Building Number (If Applicable)", "buildingId", false),
        new GeoField("Latitude", "lat", false),
        new GeoField("Longitude", "long", false),
        new TextField("Phone", "phone", false),
        new TextField("Email", "email", false),
        new TextField("Opening Hours: Mon", "monOpening", false),
        new TextField("Opening Hours: Tue", "tueOpening", false),
        new TextField("Opening Hours: Wed", "wedOpening", false),
        new TextField("Opening Hours: Thur", "thurOpening", false),
        new TextField("Opening Hours: Fri", "friOpening", false),
        new TextField("Opening Hours: Sat", "satOpening", false),
        new TextField("Opening Hours: Sun", "sunOpening", false)
    )),

    new ObjectSchema("Other", array(
        new TextField("Description", "description", false),
    ))
);