<?php
    $categories = array("Building", "Portal", "Image", "Water Source", "Showers", "Lecture Room");
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />

    <link rel="stylesheet" type="text/css" href="css/tool.css" media="all" />
    <script type="text/javascript" src="js/jquery-3.0.0.js"></script>

    <link rel="stylesheet" type="text/css" href="leaflet/leaflet.css" />
    <script type="text/javascript" src="leaflet/leaflet.js"></script>
    <script type="text/javascript" src="js/clickymap.js"></script>

    <script type="text/javascript" src="js/tool.js"></script>
</head>
<body class="app-holder">

<div class="center-block center-text">
    <div id="geo_container">
        <div class="height_control"></div>
        <div id="geo_status"></div>
    </div>
    <p id="geo_status_text">Tool not initialised</p>
</div>

<div class="center-block" id="map">

</div>

<form id="data-form">
</form>
<div class="center-block center-text" id="submit-status"></div>

</body>
</html>