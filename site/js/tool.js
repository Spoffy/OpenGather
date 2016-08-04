window.openDataGatherer = function() {
	var root = {};

    root.DATA_URL = "record_data.php";
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

        $("#geo_status").click(function (e) {
            root.pollGeo();
        });

        root.watchGeo();
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