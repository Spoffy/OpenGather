// Original Clickymap code by Christopher Gutteridge cjg@ecs.soton.ac.uk
// Clickymap is public domain. Free to modify and reuse for any purpose.
// (Leaflet & jQuery are still subject to their respective licenses)

var clickymap =  function() {
    var root = {

        onReady: function () {
            root.map = L.map('map').setView([50.93564, -1.39614], 17);
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
        }
    };

    return root;
}();

$(document).ready(clickymap.onReady);