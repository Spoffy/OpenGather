// Original Clickymap code by Christopher Gutteridge cjg@ecs.soton.ac.uk
// Clickymap is public domain. Free to modify and reuse for any purpose.
// (Leaflet & jQuery are still subject to their respective licenses)

window.clickymap =  function() {
    var root = {
        DEFAULT_MAP_POSITION: [50.93564, -1.39614],
        DEFAULT_ZOOM: 17,

        onReady: function () {
            root.map = L.map('map').setView(root.DEFAULT_MAP_POSITION, root.DEFAULT_ZOOM);
            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
                maxZoom: 19
            }).addTo(root.map);

            root.map.on('click', root.onMapClick);
        },

        onMapClick: function (e) {
            var RES = 100000; // rounding
            var lat = Math.round(e.latlng.lat * RES) / RES;
            var lng = Math.round(e.latlng.lng * RES) / RES;

            console.log("Lat: " + lat + " Long:" + lng);

            root.setClickMarkerPosition(e.latlng);
        },

        getClickMarker: function () {
            root.marker = root.marker || L.marker(root.DEFAULT_MAP_POSITION).addTo(root.map);
            return root.marker;
        },

        setClickMarkerPosition: function(latLng) {
            return root.getClickMarker().setLatLng(latLng);
        },

        setMapCentre: function(lat, lng) {
            if(root.map != null) {
                root.map.setView([lat, lng]);
            }
        }
    };

    return root;
}();

$(document).ready(clickymap.onReady);