var openDataGatherer = function() {
	var root = {};
	root.onReady = function() {
		$("#data-form").submit(function(e) {
			console.log("Fire!");
			e.preventDefault();
		});

        $("#geo_status").click(function() {
            root.pollGeo();
        });

        root.pollGeo();
	};

    root.GEO_OPTIONS = {
        enableHighAccuracy: true
    };

	root.GEO_STATUSES = {
		ACQUIRING: 1,
		FAILED: 2,
		SUCCEEDED: 3
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

	root.pollGeo = function() {
		if("geolocation" in navigator) {
            root.setGeoStatus(root.GEO_STATUSES.ACQUIRING);
            root.setGeoStatusMessage("Acquiring position...");
            root.setGeoLocationMessage("");

			navigator.geolocation.getCurrentPosition(function(pos) {
				root.setGeoStatus(root.GEO_STATUSES.SUCCEEDED);
                root.setGeoStatusMessage("Position acquired with accuracy of " + pos.coords.accuracy + "m");
				root.setGeoLocationMessage("Pos: " + pos.coords.latitude + " " + pos.coords.longitude);
			}, function(error) {
				root.setGeoStatus(root.GEO_STATUSES.FAILED);
                root.setGeoStatusMessage(error.message);
			}, root.GEO_OPTIONS);
		} else {
			root.setGeoStatus("Geolocation not available");
		}
	};
	
    return root;
}();

$(document).ready(openDataGatherer.onReady);