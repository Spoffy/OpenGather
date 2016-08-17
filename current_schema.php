<?php
$base_path = __DIR__;
require_once("$base_path/src/schema.php");

$accessOptions = array("Unknown", "Open", "Card Scanner", "Key");
$openingOptions = array("Manual - Push/Pull", "Sensor + Automatic", "Pushbutton + Automatic");

global $schemas;
$schemas = array(
    new ObjectSchema("Building Entrance", array(
        new TextField("Building Number", "buildingId", false),
        new TextField("Entrance Label", "entranceId", false),
        new TextField("Description", "description", false),
        new GeoField("Latitude", "lat", false),
        new GeoField("Longitude", "long", false),
        new DropdownField("Access Method Daytime", "accessDaytime", $accessOptions),
        new DropdownField("Access Method Evening", "accessEvening", $accessOptions),
        new DropdownField("Opening Method", "openingMethod", $openingOptions)
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