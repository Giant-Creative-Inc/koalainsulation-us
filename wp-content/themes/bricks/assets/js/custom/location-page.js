$ = jQuery;
window.addEventListener("load", function () {
  
  var select = document.getElementById("stateSelect");
  var accordionSections = document.querySelectorAll(".acc1");

  // // Populate select options with state names
  accordionSections.forEach(function (section) {
    var stateName = section.querySelector(".state-name").innerText;
    var option = document.createElement("option");
    option.value = stateName.toLowerCase().replace(/s/g, "-");
    option.text = stateName;
    select.appendChild(option);
  });

  if (select) {
    // Event listener for filtering accordion sections
    select.addEventListener("change", function () {
      var selectedState = this.value.toLowerCase();
      accordionSections.forEach(function (section) {
        var stateName = section
          .querySelector(".state-name")
          .innerText.toLowerCase()
          .replace(/s/g, "-");
        if (selectedState === "" || stateName === selectedState) {
          section.style.display = "block";
        } else {
          section.style.display = "none";
        }
      });
    });
  }

  var locations = [];
  var dynPlaces = document.querySelectorAll(".info_content");
  dynPlaces.forEach(function (elem) {
    var place = [];
    var title = elem.querySelector(".place-title").innerText;
    var pAddress = elem.querySelector(".place-address").innerText;
    var pLat = Number(elem.querySelector(".lat").innerText);
    var pLong = Number(elem.querySelector(".long").innerText);
    var pPhone = elem.querySelector(".mobile-number").innerText;
    var pZip = elem.querySelector(".zipcode").innerText;
    var anchorElement = elem.querySelector(".place-link");
    var hrefLink = anchorElement ? anchorElement.getAttribute("href") : "";
    var additionalZipcodes = elem.querySelector(
      ".additional-zipcodes"
    ).innerText;
    var pServiceAreas = elem.querySelector(".service-area").innerText;

    place.push(
      title,
      pLat,
      pLong,
      pAddress,
      pPhone,
      pZip,
      hrefLink,
      additionalZipcodes,
      pServiceAreas
    );
    locations.push(place);
  });
  //console.log(locations);
  var gmarkers = [];
  var mapCanvas = document.getElementById("map1");


if (mapCanvas) {
  // var map = new google.maps.Map(document.getElementById("map1"), {
  var map = new google.maps.Map(mapCanvas, {
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
}

  function ZoomAndCenterMyLocation(lat, long) {
    map.setCenter(new google.maps.LatLng(lat, long));
    map.setZoom(10);
  }

  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    } else {
      // console.log("Geolocation is not supported by this browser.");
    }
  }

  function showPosition(position) {
    var lat = position.coords.latitude;
    var long = position.coords.longitude;
    ZoomAndCenterMyLocation(lat, long);
  }

  function searchByZipCodeAndCreateMarker(zips, closestZip) {
    // Ensure zips is an array and sanitize it
    if (!Array.isArray(zips)) {
        zips = [zips];
    }
    zips = zips.map(function (zip) {
        return zip.trim();
    });

    // Clear existing markers
    gmarkers.forEach(function (marker) {
        marker.setMap(null);
    });
    gmarkers = [];

    //Identify all locations that matched the search (Primary list)
    var matchingLocations = locations.filter(function (location) {
        var primaryZip = location[5].trim();
        return zips.includes(primaryZip);
    });

    if (matchingLocations.length > 0) {
        var locationToHighlight = null;
        var targetMarker = null;

        // Determine the "Winner" (the specific location that services the closestZip)
        if (closestZip !== null) {
            locationToHighlight = locations.find(function (location) {
                var primaryZip = location[5].trim();
                const additionalZipCodes = location[7] ?
                    location[7].split(",").map((zip) => zip.trim()) : [];
                var allZipsForThisLocation = [primaryZip, ...additionalZipCodes];
                return allZipsForThisLocation.includes(closestZip);
            });
        }

        // Fallback to the first matched location if no specific winner found
        if (!locationToHighlight) {
            locationToHighlight = matchingLocations[0];
        }

        // Create markers for ALL matched locations
        matchingLocations.forEach(function (location) {
            var marker = createMarker(
                new google.maps.LatLng(location[1], location[2]),
                location[0],
                location[3],
                location[4],
                location[6],
                location[8]
            );
            gmarkers.push(marker);

            // Capture the specific marker object that matches our "Winner"
            if (location[0] === locationToHighlight[0]) {
                targetMarker = marker;
            }
        });

        //If we found our target marker, open the InfoWindow on it
        if (targetMarker) {
            infowindow.setContent(
                "<div>" +
                '<p style="color: white; font-size: 18px; font-weight: 900; line-height: 27px; margin-bottom: 0px;">Koala Insulation of</p>' +
                '<p style="color: white; font-size: 18px; font-weight: 900;line-height: 27px; margin-bottom: 4px;">' +
                locationToHighlight[0] +
                "</p>" +
                '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 4px;">' +
                locationToHighlight[3] +
                "</p>" +
                `<a href="tel:${locationToHighlight[4]}" style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 12px;">` +
                locationToHighlight[4] +
                "</a>" +
                '<p style="color: white; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 4px;">Common Areas Serviced</p>' +
                '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 8px; text-transform: uppercase;">' +
                locationToHighlight[8] +
                "</p>" +
                '<a href="' + locationToHighlight[6] + '" style="text-decoration: none;">' +
                '<button style="background-color: #97D700; color: #002E5D; font-size: 12px; line-height:18px;font-weight: 700; text-transform:uppercase; letter-spacing:-3%; padding: 8px 16px; border: none; border-radius: 999px; cursor: pointer;">Visit website</button>' +
                "</a>" +
                "</div>"
            );

            infowindow.open(map, targetMarker);
            map.setCenter(targetMarker.getPosition());
            map.setZoom(8);
        }

        handleScrollAndHighlightPrimaryZips(zips, closestZip);
    } else {
        alert("Unfortunately we do not service your area at this time");
    }
}

  function searchByAdditionalZipCodeAndCreateMarker(zips, closestZip) {
    // Ensure zips is an array and sanitize it
    if (!Array.isArray(zips)) {
      zips = [zips];
    }
    zips = zips.map((zip) => zip.trim());

    // Clear existing markers
    gmarkers.forEach((marker) => marker.setMap(null));
    gmarkers = [];

    // Filter locations by matching ZIP codes
    const foundLocations = locations.filter((location) => {
      const zipCodes = location[7]
        ? location[7].split(",").map((zip) => zip.trim())
        : [];
      return zips.some((zip) => zipCodes.includes(zip));
    });

    if (foundLocations.length > 0) {
      // Create markers for matched locations
      foundLocations.forEach((location) => {
        const marker = createMarker(
          new google.maps.LatLng(location[1], location[2]),
          location[0],
          location[3],
          location[4],
          location[6],
          location[8]
        );
        gmarkers.push(marker);
      });

      // Show info window for the first matched location
      const firstLocation = foundLocations[0];
      const firstMarker = gmarkers[0];
      infowindow.setContent(
        "<div>" +
        '<p style="color: white; font-size: 18px; font-weight: 900; line-height: 27px; margin-bottom: 0px;">Koala Insulation of</p>' +
        '<p style="color: white; font-size: 18px; font-weight: 900;line-height: 27px; margin-bottom: 4px;">' +
        firstLocation[0] +
        "</p>" +
        '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 4px;">' +
        firstLocation[3] +
        "</p>" +
        `<a href="tel:${firstLocation[4]}" style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 12px;">` +
        firstLocation[4] +
        "</a>" +
        '<p style="color: white; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 4px;">Common Areas Serviced</p>' +
        '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 8px; text-transform: uppercase;">' +
        firstLocation[8] +
        "</p>" +
        '<a href="' +
        firstLocation[6] +
        '" style="text-decoration: none;">' +
        '<button style="background-color: #97D700; color: #002E5D; font-size: 12px; line-height:18px;font-weight: 700; text-transform:uppercase; letter-spacing:-3%; padding: 8px 16px; border: none; border-radius: 999px; cursor: pointer;">Visit website</button>' +
        "</a>" +
        "</div>"
      );
      infowindow.open(map, firstMarker);

      map.setCenter(
        new google.maps.LatLng(firstLocation[1], firstLocation[2])
      );
      map.setZoom(4);

      // Scroll to and highlight matching elements
      handleScrollAndHighlightAdditionalZips(zips);
    } else {
      alert("Unfortunately we do not service your area at this time.");
    }
  }

  function handleScrollAndHighlightAdditionalZips(zips) {
    const infoContentArray = Array.from(
      document.querySelectorAll(".info_content")
    ).filter((element) => {
      const additionalZips = element.querySelector(".additional-zipcodes")
        ? element
          .querySelector(".additional-zipcodes")
          .innerText.split(",")
          .map((zip) => zip.trim())
        : [];
      return zips.some((zip) => additionalZips.includes(zip));
    });

    if (infoContentArray.length > 0) {
      // Scroll to the first matched element
      const firstElement = infoContentArray[0];
      document.querySelector("#brxe-htrqiy").scrollTo({
        top:
          firstElement.offsetTop -
          document.querySelector("#brxe-htrqiy").offsetTop,
        behavior: "smooth",
      });

      // Highlight all matched elements
      infoContentArray.forEach((element) => {
        element.style.backgroundColor = "white";
      });
    }
  }

  function handleScrollAndHighlightPrimaryZips(zips, closestZip) {
    const infoContentArray = Array.from(
      document.querySelectorAll(".info_content")
    ).filter((element) => {
      var primaryZip = element.querySelector(".zipcode").innerText.trim();
      return zips.some((zip) => primaryZip === zip);
    });

    if (infoContentArray.length > 0) {
      // Highlight all matched elements
      infoContentArray.forEach((element) => {
        element.style.backgroundColor = "white";
      });
    }

    if (closestZip !== null) {
      const infoContentArray = Array.from(
        document.querySelectorAll(".info_content")
      ).filter((element) => {
        var primaryZip = element.querySelector(".zipcode").innerText.trim();
        const additionalZips = element.querySelector(".additional-zipcodes")
          ? element
            .querySelector(".additional-zipcodes")
            .innerText.split(",")
            .map((zip) => zip.trim())
          : [];

        allZips = [...primaryZip, ...additionalZips];
        return allZips.includes(closestZip);
      });

      if (infoContentArray.length > 0) {
        // Scroll to the first matched element
        const firstElement = infoContentArray[0];
        document.querySelector("#brxe-htrqiy").scrollTo({
          top:
            firstElement.offsetTop -
            document.querySelector("#brxe-htrqiy").offsetTop,
          behavior: "smooth",
        });
      }
    } else {
      // Scroll to the first matched element
      const firstElement = infoContentArray[0];
      document.querySelector("#brxe-htrqiy").scrollTo({
        top:
          firstElement.offsetTop -
          document.querySelector("#brxe-htrqiy").offsetTop,
        behavior: "smooth",
      });
    }
  }

  $(document).ready(function () {
    $("#current-loc").click(function () {
      getLocation();
    });

    $("#search-zip").click(function () {
      var zip = $("#zipcode-input").val().trim();
      if (zip) {
        const zipCode = zip;
        const radius = 60;
        const apiKey =
          "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII";

        var locations = document.querySelectorAll(".info_content");
        var matchedZips = [];
        var matchedAdditionalZips = [];
        const matchedZipcodesArr = [];

        locations.forEach(function (location) {
          // Get the main ZIP code and trim it
          var zipcode = location.querySelector(".zipcode").textContent.trim();

          // Get and trim additional ZIP codes
          var additionalZipcodes = location
            .querySelector(".additional-zipcodes")
            .textContent.trim()
            .split(/s*,s*/) // Split on commas with optional spaces
            .map(function (zip) {
              return zip.trim(); // Trim each ZIP code to remove leading/trailing spaces
            });

          console.log("Checking ZIP code:", zipcode);

          // Check if the entered ZIP matches the main ZIP code
          if (zipcode === zip) {
            matchedZips.push(zipcode); // Add directly matched ZIP code
          }
          // Check if the entered ZIP is in additional ZIP codes
          else if (additionalZipcodes.includes(zip)) {
            matchedAdditionalZips.push(zip); // Add the matching entered ZIP
          }
        });

        // If direct matches found, pass them to the function
        if (matchedZips.length > 0) {
          console.log("Direct matches found for ZIP codes:", matchedZips);
          searchByZipCodeAndCreateMarker(matchedZips, null); // Pass array to the function
        } else if (matchedAdditionalZips.length > 0) {
          console.log(
            "Direct matches found for ZIP codes in additional zips:",
            matchedAdditionalZips
          );
          searchByAdditionalZipCodeAndCreateMarker(matchedAdditionalZips); // Pass array to the function
        } else {
          //initialize loader
          document.getElementById("loader-wrapper").style.display = "flex";

          console.log(
            "No direct matches found. Fetching nearby ZIP codes..."
          );

          // Make the API request for nearby ZIP codes
          fetch(koalaData.ajax_url, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
              action: "get_zip_codes_in_radius",
              // nonce: "e9a11921b7",
              zip_code: zipCode,
              radius: radius,
              api_key: apiKey,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                console.log(
                  "ZIP codes within the radius:",
                  data.data.response
                );

                if (
                  data.data.response.zip_codes &&
                  data.data.response.zip_codes.length > 0
                ) {
                  // Extract nearby ZIP codes
                  const nearbyZips = data.data.response.zip_codes.map(
                    (item) => item.zip_code
                  );
                  console.log("Nearby ZIP Codes:", nearbyZips);

                  // Check if any nearby ZIP code matches the locations
                  locations.forEach(function (location) {
                    var zipcode = location
                      .querySelector(".zipcode")
                      .textContent.trim();
                    var additionalZipcodes = location
                      .querySelector(".additional-zipcodes")
                      .textContent.trim()
                      .replace(/"/g, "") // Remove quotes around zipcodes
                      .split(/s*,s*/); // Split by commas and optional spaces

                    // Trim any extra spaces from each zipcode in the array
                    additionalZipcodes = additionalZipcodes.map((zip) =>
                      zip.trim()
                    );

                    // Combine primary and additional ZIP codes
                    const allZips = [zipcode, ...additionalZipcodes];

                    // Find matching ZIP codes
                    const matchingZips = allZips.filter((zip) =>
                      nearbyZips.includes(zip)
                    );

                    if (matchingZips.length > 0) {
                      matchedZipcodesArr.push(...matchingZips); // Add all matching ZIP codes
                      matchedZips.push(zipcode); // Add only the primary ZIP code of the location
                    }

                    console.log(
                      "All matching ZIP Codes:",
                      matchedZipcodesArr
                    );
                    console.log(
                      "Primary ZIP Codes of matching locations:",
                      matchedZips
                    );
                  });

                  if (matchedZipcodesArr.length > 0) {
                    console.log(
                      "Matches found for nearby ZIP codes:",
                      matchedZipcodesArr
                    );
                    fetch(
                      koalaData.ajax_url,
                      {
                        method: "POST",
                        headers: {
                          "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: new URLSearchParams({
                          action: "get_zip_codes_distance_in_miles",
                          input_zip: zip,
                          nearby_zips: JSON.stringify(matchedZipcodesArr), // Send as JSON string
                        }),
                      }
                    )
                      .then((response) => response.json())
                      .then((data) => {
                        //hide loader
                        document.getElementById(
                          "loader-wrapper"
                        ).style.display = "none";

                        if (!data.success) {
                          throw new Error(
                            data.data.message || "Failed to fetch distances"
                          );
                        }

                        console.log("Distance Data:", data.data);

                        // Extract zip codes in the sorted order
                        const sortedZipCodes = data.data.map(
                          (item) => item.zip
                        );

                        console.log("Sorted nearby zips:", sortedZipCodes[0]);
                        let closestZip = sortedZipCodes[0];

                        searchByZipCodeAndCreateMarker(
                          matchedZips,
                          closestZip
                        ); // Pass array to the function
                      })
                      .catch((error) => {
                        //hide loader
                        document.getElementById(
                          "loader-wrapper"
                        ).style.display = "none";

                        console.error(
                          "Error fetching zip code distances:",
                          error
                        );
                      });
                  } else {
                    //hide loader
                    document.getElementById("loader-wrapper").style.display =
                      "none";

                    alert(
                      "Unfortunately we do not service your area at this time"
                    );
                  }
                } else {
                  //hide loader
                  document.getElementById("loader-wrapper").style.display =
                    "none";
                  alert("No nearby ZIP codes found.");
                }
              } else {
                //hide loader
                document.getElementById("loader-wrapper").style.display =
                  "none";
                console.error("Error fetching ZIP codes:", data.data.message);
                alert("Failed to fetch ZIP codes. Please try again.");
              }
            })
            .catch((error) => {
              //hide loader
              document.getElementById("loader-wrapper").style.display =
                "none";

              console.error("Network error:", error);
              alert(
                "Failed to fetch ZIP codes. Please check your network connection."
              );
            });
        }
      } else {
        alert("Please enter a ZIP code.");
      }
    });

    $("#zipcode-input").keydown(function (event) {
      // Check if the pressed key is "Enter"
      if (event.key === "Enter") {
        event.preventDefault(); // Prevent default form submission behavior

        var zip = $("#zipcode-input").val().trim();
        if (zip) {
          const zipCode = zip;
          const radius = 60;
          const apiKey =
            "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII";

          var locations = document.querySelectorAll(".info_content");
          var matchedZips = [];
          var matchedAdditionalZips = [];
          var matchedZipcodesArr = [];

          locations.forEach(function (location) {
            // Get the main ZIP code and trim it
            var zipcode = location
              .querySelector(".zipcode")
              .textContent.trim();

            // Get and trim additional ZIP codes
            var additionalZipcodes = location
              .querySelector(".additional-zipcodes")
              .textContent.trim()
              .split(/s*,s*/) // Split on commas with optional spaces
              .map(function (zip) {
                return zip.trim(); // Trim each ZIP code to remove leading/trailing spaces
              });

            console.log("Checking ZIP code:", zipcode);

            // Check if the entered ZIP matches the main ZIP code
            if (zipcode === zip) {
              matchedZips.push(zipcode); // Add directly matched ZIP code
            }
            // Check if the entered ZIP is in additional ZIP codes
            else if (additionalZipcodes.includes(zip)) {
              matchedAdditionalZips.push(zip); // Add the matching entered ZIP
            }
          });

          // If direct matches found, pass them to the function
          if (matchedZips.length > 0) {
            console.log("Direct matches found for ZIP codes:", matchedZips);
            searchByZipCodeAndCreateMarker(matchedZips, null); // Pass array to the function
          } else if (matchedAdditionalZips.length > 0) {
            console.log(
              "Direct matches found for ZIP codes in additional zips:",
              matchedAdditionalZips
            );
            searchByAdditionalZipCodeAndCreateMarker(matchedAdditionalZips); // Pass array to the function
          } else {
            //initialize loader
            document.getElementById("loader-wrapper").style.display = "flex";

            console.log(
              "No direct matches found. Fetching nearby ZIP codes..."
            );

            // Make the API request for nearby ZIP codes
            fetch(koalaData.ajax_url, {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded",
              },
              body: new URLSearchParams({
                action: "get_zip_codes_in_radius",
                // nonce: "e9a11921b7",
                zip_code: zipCode,
                radius: radius,
                api_key: apiKey,
              }),
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  console.log(
                    "ZIP codes within the radius:",
                    data.data.response
                  );

                  if (
                    data.data.response.zip_codes &&
                    data.data.response.zip_codes.length > 0
                  ) {
                    // Extract nearby ZIP codes
                    const nearbyZips = data.data.response.zip_codes.map(
                      (item) => item.zip_code
                    );
                    console.log("Nearby ZIP Codes:", nearbyZips);

                    // Check if any nearby ZIP code matches the locations
                    locations.forEach(function (location) {
                      var zipcode = location
                        .querySelector(".zipcode")
                        .textContent.trim();
                      var additionalZipcodes = location
                        .querySelector(".additional-zipcodes")
                        .textContent.trim()
                        .replace(/"/g, "") // Remove quotes around zipcodes
                        .split(/s*,s*/); // Split by commas and optional spaces

                      // Trim any extra spaces from each zipcode in the array
                      additionalZipcodes = additionalZipcodes.map((zip) =>
                        zip.trim()
                      );

                      // Combine primary and additional ZIP codes
                      const allZips = [zipcode, ...additionalZipcodes];

                      // Find matching ZIP codes
                      const matchingZips = allZips.filter((zip) =>
                        nearbyZips.includes(zip)
                      );

                      if (matchingZips.length > 0) {
                        matchedZipcodesArr.push(...matchingZips); // Add all matching ZIP codes
                        matchedZips.push(zipcode); // Add only the primary ZIP code of the location
                      }

                      console.log(
                        "All matching ZIP Codes:",
                        matchedZipcodesArr
                      );
                      console.log(
                        "Primary ZIP Codes of matching locations:",
                        matchedZips
                      );
                    });

                    if (matchedZipcodesArr.length > 0) {
                      console.log(
                        "Matches found for nearby ZIP codes:",
                        matchedZipcodesArr
                      );
                      fetch(
                        koalaData.ajax_url,
                        {
                          method: "POST",
                          headers: {
                            "Content-Type":
                              "application/x-www-form-urlencoded",
                          },
                          body: new URLSearchParams({
                            action: "get_zip_codes_distance_in_miles",
                            input_zip: zip,
                            nearby_zips: JSON.stringify(matchedZipcodesArr), // Send as JSON string
                          }),
                        }
                      )
                        .then((response) => response.json())
                        .then((data) => {
                          //hide loader
                          document.getElementById(
                            "loader-wrapper"
                          ).style.display = "none";

                          if (!data.success) {
                            throw new Error(
                              data.data.message || "Failed to fetch distances"
                            );
                          }

                          console.log("Distance Data:", data.data);

                          // Extract zip codes in the sorted order
                          const sortedZipCodes = data.data.map(
                            (item) => item.zip
                          );

                          console.log(
                            "Sorted nearby zips:",
                            sortedZipCodes[0]
                          );
                          let closestZip = sortedZipCodes[0];

                          searchByZipCodeAndCreateMarker(
                            matchedZips,
                            closestZip
                          ); // Pass array to the function
                        })
                        .catch((error) => {
                          //hide loader
                          document.getElementById(
                            "loader-wrapper"
                          ).style.display = "none";

                          console.error(
                            "Error fetching zip code distances:",
                            error
                          );
                        });
                    } else {
                      //hide loader
                      document.getElementById(
                        "loader-wrapper"
                      ).style.display = "none";

                      alert(
                        "Unfortunately we do not service your area at this time"
                      );
                    }
                  } else {
                    //hide loader
                    document.getElementById("loader-wrapper").style.display =
                      "none";
                    alert("No nearby ZIP codes found.");
                  }
                } else {
                  //hide loader
                  document.getElementById("loader-wrapper").style.display =
                    "none";
                  console.error(
                    "Error fetching ZIP codes:",
                    data.data.message
                  );
                  alert("Failed to fetch ZIP codes. Please try again.");
                }
              })
              .catch((error) => {
                //hide loader
                document.getElementById("loader-wrapper").style.display =
                  "none";

                console.error("Network error:", error);
                alert(
                  "Failed to fetch ZIP codes. Please check your network connection."
                );
              });
          }
        } else {
          alert("Please enter a ZIP code.");
        }
      }
    });
  });

  var infowindow = new google.maps.InfoWindow({
    maxWidth: 285,
  });

  function createMarker(
    latlng,
    title,
    pAddress,
    pPhone,
    hrefLink,
    pServiceAreas
  ) {
    var marker = new google.maps.Marker({
      position: latlng,
      map: map,
      icon: koalaData.map_pin,
      title: title 
    });

    google.maps.event.addListener(marker, "click", function () {
      infowindow.setContent(
        "<div>" +
        '<p style="color: white; font-size: 18px; font-weight: 900; line-height: 27px; margin-bottom: 0px;">Koala Insulation of</p>' +
        '<p style="color: white; font-size: 18px; font-weight: 900;line-height: 27px; margin-bottom: 4px;">' +
        title +
        "</p>" +
        '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 4px;">' +
        pAddress +
        "</p>" +
        `<a href="tel:${pPhone}" style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 12px;">` +
        pPhone +
        "</a>" +
        '<p style="color: white; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 4px;">Common Areas Serviced</p>' +
        '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 8px; text-transform: uppercase;">' +
        pServiceAreas +
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
      map.setZoom(4);
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
      location[8]
    );
    gmarkers.push(marker);
  });

  $("#zipcode-input").click(function () {
    var zip = $(this).val().trim();
    if (zip === "") {
      resetMap();
      // Scroll the container back to the top
      document.querySelector("#brxe-htrqiy").scrollTo({
        top: 0,
        behavior: "smooth",
      });

      // Reset the background color of all highlighted elements
      var highlightedElements = document.querySelectorAll(".info_content");
      highlightedElements.forEach(function (element) {
        element.style.backgroundColor = "rgba(255, 255, 255, 0.1)";
      });
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
        location[8]
      );
      gmarkers.push(marker);
    });
  }
});