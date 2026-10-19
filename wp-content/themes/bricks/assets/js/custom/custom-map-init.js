$ = jQuery;
window.addEventListener("load", function () {
    var locations = [];

    var dnyPlaces = estimateData.zip_locations;

    dnyPlaces.forEach(location => {
        var place = [];
        var title = location.title;
        var pAddress = location.location_address;
        var pLat = location.lat;
        var pLong = location.long;
        var pPhone = location.phone;
        var pZip = location.title;
        var pService = location.location_service;
        // var anchorElement = elem.querySelector(".place-link");
        var hrefLink = location.url;
        place.push(
            title,
            pLat,
            pLong,
            pAddress,
            pPhone,
            pZip,
            hrefLink,
            pService
        );
        locations.push(place);
    });

    var gmarkers = [];
    var map = new google.maps.Map(document.getElementById("map1"), {
        zoom: 4,
        center: new google.maps.LatLng(37.09024, -95.712891),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [
            { elementType: "geometry", stylers: [{ color: "#9ec7ba" }] },
            { elementType: "labels.icon", stylers: [{ visibility: "off" }] },
            { elementType: "labels.text.fill", stylers: [{ color: "#fcfcff" }] },
            { elementType: "labels.text.stroke", stylers: [{ visibility: "off" }] },
            {
                featureType: "administrative",
                elementType: "geometry",
                stylers: [{ color: "#9ec7ba" }],
            },
            {
                featureType: "administrative.country",
                elementType: "labels.text.fill",
                stylers: [{ visibility: "off" }],
            },
            {
                featureType: "administrative.land_parcel",
                stylers: [{ visibility: "off" }],
            },
            {
                featureType: "administrative.locality",
                elementType: "labels.text.fill",
                stylers: [{ color: "#fcfcff" }],
            },
            {
                featureType: "poi",
                elementType: "labels.text.fill",
                stylers: [{ color: "#fcfcff" }],
            },
            {
                featureType: "poi.park",
                elementType: "geometry",
                stylers: [{ color: "#99a9bb" }],
            },
            {
                featureType: "poi.park",
                elementType: "labels.text.fill",
                stylers: [{ color: "#fcfcff" }],
            },
            {
                featureType: "road",
                elementType: "geometry.fill",
                stylers: [{ color: "#7EB4A3" }],
            },
            {
                featureType: "road.arterial",
                elementType: "geometry",
                stylers: [{ color: "#7EB4A3" }],
            },
            {
                featureType: "road.highway",
                elementType: "geometry",
                stylers: [{ color: "#7EB4A3" }],
            },
            {
                featureType: "road.highway.controlled_access",
                elementType: "geometry",
                stylers: [{ color: "#7EB4A3" }],
            },
            {
                featureType: "transit",
                elementType: "labels.text.fill",
                stylers: [{ color: "#fcfcff" }],
            },
            {
                featureType: "water",
                elementType: "geometry",
                stylers: [{ color: "#7EB4A3" }],
            },
        ],
    });

    function ZoomAndCenterMyLocation(lat, long) {
        map.setCenter(new google.maps.LatLng(lat, long));
        map.setZoom(15);
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            console.log("Geolocation is not supported by this browser.");
        }
    }

    function showPosition(position) {
        var lat = position.coords.latitude;
        var long = position.coords.longitude;
        ZoomAndCenterMyLocation(lat, long);
    }

    jQuery(document).ready(function ($) {
        $("#current-loc").click(function () {
            getLocation();
        });

        $("#search-zip").click(function () {
            var zip = $("#zipcode-input").val().trim();
            if (zip) {
                searchByZipCode(zip);
            } else {
                alert("Please enter a zip code.");
            }
        });

        function searchByZipCode(zip) {
            // Clear existing markers
            gmarkers.forEach(function (marker) {
                marker.setMap(null);
            });
            gmarkers = [];

            var foundLocations = locations.filter(function (location) {
                return location[5] === zip;
            });

            if (foundLocations.length > 0) {
                foundLocations.forEach(function (location) {
                    var marker = createMarker(
                        new google.maps.LatLng(location[1], location[2]),
                        location[0],
                        location[3],
                        location[4],
                        location[6],
                        location[7]
                    );
                    gmarkers.push(marker);
                });
            } else {
                alert("No location found for the entered zip code.");
            }
        }
    });

    var infowindow = new google.maps.InfoWindow({
        maxWidth: 285,
    });

    function createMarker(latlng, title, pAddress, pPhone, hrefLink, pService) {
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            icon: koalaData.map_pin,
        });

        google.maps.event.addListener(marker, "click", function () {
            infowindow.setContent(
                "<div>" +
                '<p style="color: white; font-size: 18px; font-weight: 900; line-height: 27px; margin-bottom: 4px;">' +
                "Koala Insulation of " +
                title +
                "</p>" +
                '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 4px;">' +
                pAddress +
                "</p>" +
                '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 12px;">' +
                pPhone +
                "</p>" +
                '<p style="color: white; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 4px;">Common Areas Serviced</p>' +
                '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 8px; text-transform: uppercase;">' +
                pService +
                "</p>" +
                '<a href="' +
                hrefLink +
                '" style="text-decoration: none;">' +
                '<button style="background-color: #97D700; color: #002E5D; font-size: 12px; line-height:18px;font-weight: 700; text-transform:uppercase; letter-spacing:-3%; padding: 8px 16px; border: none; border-radius: 999px; cursor: pointer;">Visit website</button>' +
                "</a>" +
                "</div>"
            );

            infowindow.setPosition(latlng);
            infowindow.open(map, marker);

            // Zoom and pan to the marker when it's clicked
            map.setZoom(10);
            map.panTo(latlng);
        });

        return marker;
    }

    locations.forEach(function (location) {
        var marker = createMarker(
            new google.maps.LatLng(location[1], location[2]),
            location[0],
            location[3],
            location[4],
            location[6],
            location[7]
        );
        gmarkers.push(marker);
    });

    $("#zipcode-input").on("input", function () {
        var zip = $(this).val().trim();
        if (zip === "") {
            resetMap();
        }
    });

    $("#zipcode-input").click(function () {
        var zip = $(this).val().trim();
        if (zip === "") {
            resetMap();
        }
    });

    function resetMap() {
        // Clear existing markers
        gmarkers.forEach(function (marker) {
            marker.setMap(null);
        });
        gmarkers = [];

        // Reset map to initial state
        map.setZoom(4);
        map.setCenter(new google.maps.LatLng(37.09024, -95.712891));

        // Create markers for each location in the locations array
        locations.forEach(function (location) {
            var marker = createMarker(
                new google.maps.LatLng(location[1], location[2]),
                location[0],
                location[3],
                location[4],
                location[6],
                location[7]
            );
            gmarkers.push(marker);
        });
    }
});