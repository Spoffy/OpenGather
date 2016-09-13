<?php
$base_path = __DIR__;
require_once("$base_path/src/schema.php");

$accessOptions = array("Unknown", "Open", "Keycode", "Card Scanner", "Key");
$openingOptions = array("Manual - Push/Pull", "Pushbutton + Automatic", "Sensor + Automatic", "Other automatic", "Rotating");
$roomTypes = array("Gender-neutral Toilet", "Lecture Room", "Public Shower", "Other");

global $schemas;
$schemas = array(
    new ObjectSchema("Building Entrance", array(
        new TextField("Building Number", "buildingId", true),
        new TextField("Entrance Label", "entranceId", false),
        new TextField("Description", "description", true),
        new LatitudeField(true),
        new LongitudeField(true),
        new DropdownField("Access Method Daytime", "accessDaytime", $accessOptions),
        new DropdownField("Access Method Evening", "accessEvening", $accessOptions),
        new DropdownField("Opening Method", "openingMethod", $openingOptions)
    )),

    new ObjectSchema("Image (Building)", array(
        new TextField("Building Number", "buildingId", true),
        new LatitudeField(true),
        new LongitudeField(true)
    )),

    new ObjectSchema("Drinking Water Source", array(
        new TextField("Building Number", "buildingId", true),
        new TextField("Floor", "floor", true),
        new LatitudeField(true),
        new LongitudeField(true)
    )),

    new ObjectSchema("Point of Service", array(
        new TextField("Description", "description", true),
        new TextField("Building Number (If Applicable)", "buildingId", false),
        new LatitudeField(true),
        new LongitudeField(true),
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

    new ObjectSchema("Room", array(
        new TextField("Building Number", "buildingId", true),
        new TextField("Room Number", "roomNumber", false),
        new TextField("Room Name", "roomName", true),
        new LatitudeField(true),
        new LongitudeField(true),
        new DropdownField("Type", "type", $roomTypes, true)
    )),

    new ObjectSchema("Bike Storage", array(
        new TextField("Additional Info", "description", false),
        new LatitudeField(true),
        new LongitudeField(true)
    )),

    new ObjectSchema("Other", array(
        new TextField("Description", "description", true),
    ))
);