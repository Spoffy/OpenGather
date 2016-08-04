window.openDataGatherer = function() {
	var root = {};

    root.DATA_URL = "record_data.php";
    root.SCHEMA_URL = "ajax/schemajson.php";
    root.LOCAL_CACHE_KEY = "opengather_local_backup";

    root.GEO_OPTIONS = {
        enableHighAccuracy: true
    };

    root.GEO_STATUSES = {
        ACQUIRING: 1,
        FAILED: 2,
        SUCCEEDED: 3
    };

	root.onReady = function() {
		$("#data-form").submit(function(e) {
			e.preventDefault();
            console.log(root.getData());
            root.postData(root.getData());
		});

        root.initialiseForm();

        $("#geo_status").click(function (e) {
            root.pollGeo();
        });

        root.watchGeo();
	};

    //TODO Make this serverside or template it nicer.
    //This definitely needs less HTML in it.
    //This should almost all be generated serverside and only switched out in the browser, I think.
    //That way we can use PHP templating... Alternatively Angular/React but that's heavyweight.
    root.initialiseForm = function() {
        var form = $("#data-form");
        $.get({
            url: root.SCHEMA_URL,
            dataType: "json",
            success: function (schema) {
                root.schema = schema;
                form.append('<label for="type">Object Type</label>' +
                    '<select class="form-field" id="type">' +
                    '</select>' +
                    '<br/>');
                for (var fieldId in root.schema.fields) {
                    var html = root.schema.fields[fieldId].html;
                    form.append(html);
                }
                form.append('<input type="submit" class="form-field" value="Log Data" id="submit" />');
            },
            error: function () {
                form.append("<p> No schema available, unable to create form.</p>")
            }
        });
    };

	root.setGeoStatusMessage = function(statusMessage) {
		$("#geo_status_text").text(statusMessage);
	};

	root.setGeoStatus = function(status) {
		var geoStatusDom = $("#geo_status");
		switch(status) {
			case root.GEO_STATUSES.ACQUIRING:
				geoStatusDom.css("background-color", "yellow");
				break;
			case root.GEO_STATUSES.FAILED:
				geoStatusDom.css("background-color", "red");
				break;
			case root.GEO_STATUSES.SUCCEEDED:
				geoStatusDom.css("background-color", "green");
				break;
		}
	};

    root.setGeoLocationMessage = function(statusMessage) {
        $("#location").text(statusMessage);
    };

    //TODO Unify location format
    root.onGeoSuccess = function(pos) {
        root.setGeoStatus(root.GEO_STATUSES.SUCCEEDED);
        root.setGeoStatusMessage("Position acquired with accuracy of " + pos.coords.accuracy + "m");
        root.setGeoLocationMessage("Pos: " + pos.coords.latitude + " " + pos.coords.longitude);
        root.lastPosition = pos;
        window.clickymap.setMapCentre(pos.coords.latitude, pos.coords.longitude);
    };

    root.onGeoError = function(error) {
        root.setGeoStatus(root.GEO_STATUSES.FAILED);
        root.setGeoStatusMessage(error.message);
    };

    root.performGeo = function(geoRequestFunction) {
        if("geolocation" in navigator) {
            root.setGeoStatus(root.GEO_STATUSES.ACQUIRING);
            root.setGeoStatusMessage("Acquiring position...");
            root.setGeoLocationMessage("");

            geoRequestFunction(
                root.onGeoSuccess,
                root.onGeoError,
                root.GEO_OPTIONS
            );

        } else {
            root.setGeoStatusMessage("Geolocation not available");
        }
    };

    root.watchGeo = root.performGeo.bind(root, navigator.geolocation.watchPosition.bind(navigator.geolocation));
    root.pollGeo = root.performGeo.bind(root, navigator.geolocation.getCurrentPosition.bind(navigator.geolocation));
    
    root.getPosition = function () {
        var markerCoords = window.clickymap.getMarkerCoords();
        if(markerCoords) {
            return {
                lat: markerCoords.lat,
                long: markerCoords.lng,
                accuracy: 0
            }
        }

        if(root.lastPosition) {
            return {
                lat: root.lastPosition.coords.latitude,
                long: root.lastPosition.coords.longitude,
                accuracy: root.lastPosition.coords.accuracy
            }
        }

        return null;
    };

    //TODO Enable this. A task by itself, since it'll break everything.
    root.getTimeSinceEpochSeconds = function() {
        return Math.floor(new Date().getTime()/1000);
    }
    
    root.getData = function () {
        return {
            time: (new Date()).getTime(),
            label: $("#tag").val(),
            type: $("#type").val(),
            position: root.getPosition()
        }
    };

    root.postData = function(data) {
        $("#submit-status").text("Sending data...");
        root.addDataToLocalBackup(data);

        $.post({
            url: root.DATA_URL,
            data: data,
            success: function () {
                $("#submit-status").text("Data sent successfully");
                window.clickymap.clearMarker();
            },
            error: function (req, message, errorThrown) {
                $("#submit-status").text("Failed to send data: " + message + " " + errorThrown);
            }
        });
    };

    root.getLocalBackup = function() {
        return window.localStorage.getItem(root.LOCAL_CACHE_KEY) || "[]";
    };

    root.storeLocalBackup = function(backupData) {
        return window.localStorage.setItem(root.LOCAL_CACHE_KEY, JSON.stringify(backupData));
    }

    root.addDataToLocalBackup = function(data) {
        var backup = JSON.parse(root.getLocalBackup());
        backup.push(data);
        root.storeLocalBackup(backup);
    };
	
    return root;
}();

$(document).ready(openDataGatherer.onReady);