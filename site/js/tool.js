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
        root.initialiseForm();

        $("#geo_status").click(function (e) {
            root.pollGeo();
        });

        window.clickymap.map.on('click', root.updateFormLocationIfExists);

        root.watchGeo();
	};

    root.initialiseForm = function() {
        var form = $("#data-form");
        $.get({
            url: root.SCHEMA_URL,
            dataType: "json",
            success: function (schemas) {
                root.schemas = schemas;
                if(root.schemas.length <= 0) {
                    root.onInvalidSchema();
                    return;
                }
                root.setSchema(root.schemas[0]);
            },
            error: root.onInvalidSchema
        });

        form.submit(function(e) {
            e.preventDefault();
            if(!root.validateAndHighlight()) {
                console.log("Not submitting, form failed validation");
                return;
            }
            console.log(root.getData());
            root.postData(root.getData());
        });
    };

    root.validateAndHighlight = function() {
        var success = true;
        if(root.currentSchema) {
            root.currentSchema.fields.forEach(function (field) {
                var formField = $("#" + field.formId);
                //If form field is required and empty...
                if (field.required && !formField.val()) {
                    formField.css("background", "red");
                    success = false;
                }
            });
        } else {
            success = false;
        }
        return success;
    };

    //TODO Make this serverside or template it nicer.
    //This definitely needs less HTML in it.
    //This should almost all be generated serverside and only switched out in the browser, I think.
    //That way we can use PHP templating... Alternatively Angular/React but that's heavyweight.
    //Display option via hiding, rather than actually mutating the DOM. CSS will be much nicer AND much faster.
    root.setSchema = function( newSchema ) {
        root.currentSchema = newSchema;
        var form = $("#data-form");
        form.empty();

        //Add the select box;
        form.append('<label for="type">Object Type</label>' +
            '<select class="form-field" id="schema">' +
            '</select>' +
            '<br/>');

        //Populate the select box
        var selectBox = $("#schema");
        root.schemas.forEach(function(schema) {
            selectBox.append("<option value='" + schema.name + "'>" + schema.name + "</option>");
        });
        selectBox.val(newSchema.name);
        //Be cautious we don't infinitely recurse here...
        selectBox.change(root.onSchemaChange)

        //Populate the fields
        newSchema.fields.forEach(function(field) {
            var html = field.html;
            html += "<br/>";
            form.append(html);
        });

        //Add a submit button
        form.append('<input type="submit" class="form-field" value="Log Data" id="submit" />');
    };

    root.onInvalidSchema = function() {
        $("#data-form").append("<p> No schemas available, unable to create form.</p>");
    };

    root.getSchemaByName = function(name) {
        var result = null;
        root.schemas.some(function(schema) {
            if(schema.name == name) {
                result = schema;
                return true;
            }
        });
        return result;
    };

    root.onSchemaChange = function() {
        var newSchemaName = $("#schema").val();
        root.setSchema(root.getSchemaByName(newSchemaName));
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

    //TODO Unify location format
    root.onGeoSuccess = function(pos) {
        root.setGeoStatus(root.GEO_STATUSES.SUCCEEDED);
        root.setGeoStatusMessage("Position acquired with accuracy of " + pos.coords.accuracy + "m");
        root.lastPosition = pos;
        window.clickymap.setMapCentre(pos.coords.latitude, pos.coords.longitude);
        root.updateFormLocationIfExists();
    };

    //TODO Do this without relying on form_lat and form_long
    root.updateFormLocationIfExists = function() {
        var lat_field = $("#form_lat");
        var long_field = $("#form_long");
        if(lat_field.length <= 0 || long_field.length <= 0) { return; }
        var position = root.getPosition();
        lat_field.val(position.lat);
        long_field.val(position.long);
    };

    root.onGeoError = function(error) {
        root.setGeoStatus(root.GEO_STATUSES.FAILED);
        root.setGeoStatusMessage(error.message);
    };

    root.performGeo = function(geoRequestFunction) {
        if("geolocation" in navigator) {
            root.setGeoStatus(root.GEO_STATUSES.ACQUIRING);
            root.setGeoStatusMessage("Acquiring position...");

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
        var data = {
            time: (new Date()).getTime(),
            schema: root.currentSchema.name
        };
        root.currentSchema.fields.forEach(function(field) {
            data[field.id] = $("#"+field.formId).val();
        });
        return data;
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