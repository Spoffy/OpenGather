<?php
    $categories = ["Building", "Portal", "Image"];
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/tool.css" media="all" />
<script type="text/javascript" src="js/jquery-3.0.0.js"></script>
<script type="text/javascript" src="js/tool.js"></script>
</head>
<body>
<div class="center-block center-text">
    <div id="geo_status"> </div>
    <p id="location"></p>
    <p id="geo_status_text">Tool not initialised</p>
</div>
<form id="data-form">
    <label for="type">Object</label>
	<select class="form-field" id="type">
        <?php
            foreach ($categories as $category) {
                print("<option value='$category'>$category</option>");
            }
        ?>
	</select>
    <br/>
    <label for="tag">Tag As</label>
	<input type="text" class="form-field" id="tag" />
    <br/>
	<input type="submit" class="form-field" value="Log Data" id="submit" />
</form>
<div class="center-block center-text" id="submit-status"></div>
</body>
</html>