function setPopupPhoneLink(elementId, phoneNumber) {
    document.querySelectorAll("#" + elementId).forEach(function (element) {
      const phoneHref = "tel:" + phoneNumber;
      const existingLink = element.closest("a");

      if (existingLink) {
        existingLink.href = phoneHref;
        element.textContent = phoneNumber;
        return;
      }

      const phoneLink = document.createElement("a");
      Array.from(element.attributes).forEach(function (attribute) {
        phoneLink.setAttribute(attribute.name, attribute.value);
      });
      phoneLink.href = phoneHref;
      phoneLink.textContent = phoneNumber;
      element.replaceWith(phoneLink);
    });
  }

function populateGravityLocationFields(zipCode, location) {
    getGravityQuoteFormIds().forEach(function (formId) {
      const locationFields = window.kgiData?.locationFieldIds?.[formId] || {};
      const zipFieldId = window.kgiData?.zipFieldIds?.[formId] || 6;
      const values = {
        [`input_${formId}_${zipFieldId}`]: zipCode,
        [`input_${formId}_${locationFields.locationSlug || 17}`]: location.slug,
        [`input_${formId}_${locationFields.locationId || 18}`]: location.id,
        [`input_${formId}_${locationFields.pageUrl || 19}`]: window.location.href,
      };

      Object.entries(values).forEach(function ([inputId, value]) {
        const input = document.getElementById(inputId);

        if (input) {
          input.value = value || "";
          input.dispatchEvent(new Event("input", { bubbles: true }));
          input.dispatchEvent(new Event("change", { bubbles: true }));
        }
      });
    });
  }

function getGravityQuoteFormIds() {
    const configuredFormIds = Object.keys(
      window.kgiData?.locationFieldIds || {}
    ).map(Number);
    return configuredFormIds.length ? configuredFormIds : [12, 13];
  }

function showGravityQuoteForms() {
    getGravityQuoteFormIds().forEach(function (formId) {
      const form = document.getElementById(`gform_${formId}`);
      const wrapper = document.getElementById(`gform_wrapper_${formId}`);

      if (form) form.style.display = "block";
      if (wrapper) wrapper.style.display = "block";
    });
  }

function resetGravityQuoteForms() {
    getGravityQuoteFormIds().forEach(function (formId) {
      document.getElementById(`gform_${formId}`)?.reset();
    });
  }

document.addEventListener("DOMContentLoaded", function () {
  const customService = document.getElementById("custom-service");
  const customServiceUl = document.getElementById("custom-service-ul");
  const toggleButton = customService?.querySelector("button");

  if (customService && customServiceUl && toggleButton) {
    customService.addEventListener("mouseenter", function () {
      customService.classList.add("open");
      toggleButton.setAttribute("aria-expanded", "true");
      customServiceUl.style.opacity = "1";
      customServiceUl.style.visibility = "visible";
      customServiceUl.style.minWidth = "290px";
    });

    customService.addEventListener("mouseleave", function () {
      customService.classList.remove("open");
      toggleButton.setAttribute("aria-expanded", "false");
      customServiceUl.style.opacity = "0";
      customServiceUl.style.visibility = "hidden";
    });
  }


  const body = document.body;
  const sidebar = document.querySelector('.side-bar-form-wrapper');
  const getQuoteBtns = document.querySelectorAll('.show-sidebar-form');
  const closeBtn = document.querySelector('.close-side-bar-form');

  const activeClass = 'form-active';

  // Loop through all buttons and add event listeners
  getQuoteBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      // If a parent element is wired to open a Bricks popup, let that handle it
      // instead of also opening the sidebar (they'd both fire on the same click).
      if (btn.closest('[data-interactions*="popup"]')) return;

      body.classList.add(activeClass);
      if (sidebar) {
        sidebar.classList.add(activeClass);

        // Focus the first input or textarea inside the sidebar form
        const firstInput = sidebar.querySelector('input, textarea, select, button');
        if (firstInput) {
          firstInput.focus();
        }
      }
    });
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', function () {
      body.classList.remove(activeClass);
      if (sidebar) {
        sidebar.classList.remove(activeClass);
      }
    });
  }



});



/**
 * Find My Location Button Click and Enter handled
 * Popup after Location Found and Nearest Location search as per zip code
 * Show/Hide popup
*/
document.querySelectorAll(".top-zipcode-input").forEach(function (input) {
  input.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
      var locationContainer = this.closest(".location-container");
      var inputZip = this.value.trim();

      if (inputZip === "") {
        document.getElementById("location-popup").style.display = "none";
        alert("Enter Zip or Postal Code");
        return;
      }

      const zipCode = inputZip;
      const radius = 60;
      const apiKey =
        "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII";

      // var locations = document.querySelectorAll(".single-location");

      var nearbyLocationFinalArr = [];
      // Clear previous popup content (if any)
      const popupContainer = document.getElementById(
        "location-popup-container"
      );
      popupContainer.innerHTML = ""; // Clear previous popup data
      const popupInnerStatic = document.getElementById(
        "location-popup-inner-static"
      );

      popupInnerStatic.style.display = "flex";

      // Reset form display settings at the start of each search
      showGravityQuoteForms();

      let matchFound = false;

      fetch(ajaxData.ajax_url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "match_location_by_zip",
          nonce: ajaxData.match_location_nonce,
          zip_code: inputZip,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          // console.log('data', data.data.location);
          if (data.data.matched && data.data.location) {
            const locations = data.data.location;
            console.log(locations.title);

            populateGravityLocationFields(inputZip, locations);

            document.getElementById("popup-location_title").textContent = locations.title;
            document.getElementById("popup-location_address").textContent = locations.address;
            document.getElementById("popup-location_mobile").textContent = locations.phone;
            document.getElementById("popup-location_link").href = locations.website;
            document.getElementById("location-popup").style.display = "flex";

            // document.getElementById("zip").value = inputZip;
            // document.getElementById("key").value = locations.key;
            // document.getElementById("keySm").value = locations.sm_key;
            // document.getElementById("url").value = locations.website;
            setPopupPhoneLink("est-phone-number", locations.phone);
            document.getElementById("tel-href").href = "tel:" + locations.phone;

          } else {
            console.log("No direct zip match found.");
            const allLocations = data.data.locations;
            runFallbackSearch(zipCode, radius, apiKey, allLocations);
          }
        })
        .catch((err) => {
          console.error("AJAX error:", err);
        });

      function runFallbackSearch(zipCode, radius, apiKey, allLocations) {
        if (!matchFound) {
          //initialize loader
          document.getElementById("loader-wrapper").style.display = "flex";

          var matchedZipcodesArr = [];
          document.getElementById("location-popup").style.display = "none";
          console.log("Fetching nearby ZIP codes...");

          // Make the API request
          fetch(ajaxData.ajax_url, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
              action: "get_zip_codes_in_radius",
              nonce: ajaxData.zip_code_in_radius_nonce,
              zip_code: zipCode,
              radius: radius,
              api_key: apiKey,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                console.log("ZIP codes within the radius:", data.data.response);

                if (
                  data.data.response.zip_codes &&
                  data.data.response.zip_codes.length > 0
                ) {
                  // Extract nearby ZIP codes
                  const nearbyZips = data.data.response.zip_codes.map(
                    (item) => item.zip_code
                  );
                  console.log("Nearby ZIP Codes:", nearbyZips);
                  console.log('Location:', allLocations);

                  allLocations.forEach(function (location) {
                    const zipcode = location.zipcode;
                    const additionalZipcodes = location.additional_zipcodes || [];

                    const allZips = [zipcode, ...additionalZipcodes.map(zip => zip.trim())];

                    const matchingZips = allZips.filter(zip => nearbyZips.includes(zip));

                    if (matchingZips.length > 0) {
                      console.log("Matching ZIP Codes:", matchingZips);

                      matchedZipcodesArr.push(...matchingZips);

                      nearbyLocationFinalArr.push({
                        placeTitle: location.title,
                        placeAddress: location.address,
                        mobileNumber: location.phone,
                        websiteLink: location.website,
                        locationKey: location.key,
                        locationServiceminderKey: location.sm_key,
                        locationId: location.id,
                        locationSlug: location.slug,
                        locationzipcode: zipcode,
                        matchedZipcode: matchingZips,
                      });
                    }
                  });

                  console.log(
                    "Final nearby locations array:",
                    nearbyLocationFinalArr
                  );

                  if (nearbyLocationFinalArr.length > 0) {
                    fetch(
                      ajaxData.ajax_url,
                      {
                        method: "POST",
                        headers: {
                          "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: new URLSearchParams({
                          action: "get_zip_codes_distance_in_miles",
                          input_zip: inputZip,
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

                        const sortedZipCodes = data.data.map(
                          (item) => item.zip
                        );

                        console.log("Sorted ZIP Codes:", sortedZipCodes);

                        // Reorder nearbyLocationFinalArr based on sorted zip codes
                        nearbyLocationFinalArr.sort((a, b) => {
                          // Find the first matching ZIP from sortedZipCodes in each location's matchedZipcode array
                          const indexA = sortedZipCodes.findIndex((zip) =>
                            a.matchedZipcode.includes(zip)
                          );
                          const indexB = sortedZipCodes.findIndex((zip) =>
                            b.matchedZipcode.includes(zip)
                          );

                          // If no match is found, set index to Infinity so unmatched locations go last
                          return (
                            (indexA === -1 ? Infinity : indexA) -
                            (indexB === -1 ? Infinity : indexB)
                          );
                        });

                        console.log(
                          "Sorted nearby locations:",
                          nearbyLocationFinalArr
                        );

                        // Now proceed with displaying the sorted locations
                        document.getElementById(
                          "location-popup"
                        ).style.display = "flex";
                        popupInnerStatic.style.display = "none"; // Hide static content

                        // Clear existing content before adding new locations
                        popupContainer.innerHTML = "";

                        // Add new content for each sorted location
                        nearbyLocationFinalArr.forEach((item) => {
                          const locationDiv = document.createElement("div");
                          locationDiv.classList.add("location-item");
                          locationDiv.dataset.locationId = item.locationId;
                          locationDiv.dataset.locationSlug = item.locationSlug;

                          locationDiv.innerHTML = `
        <h3 class="brxe-heading heading-style-h2 locationitem_title">${item.placeTitle}</h3>
        <h3 class="brxe-heading text-size-regular locationitem_address">${item.placeAddress}</h3>
        <h3 class="brxe-heading text-size-regular locationitem_phone">${item.mobileNumber}</h3>
        <div id="brxe-tfhrjk" class="brxe-block">
          <a href="${item.websiteLink}" class="brxe-div locationitem_link">
            <div id="brxe-xonhvx" class="brxe-text-basic">Visit Website</div>
          </a>
          <a class="brxe-div quote-btn-custom">
            <div class="brxe-text-basic"><span>Get a Free Estimate</span></div>
          </a>
          <p style="display:none;" class="seletced_location_key">${item.locationKey}</p>
          <p style="display:none;" class="seletced_location_sm_key">${item.locationServiceminderKey}</p>
          <p style="display:none;" class="location_zipcode">${item.locationzipcode}</p>
        </div>
      `;
                          popupContainer.appendChild(locationDiv);
                          document.getElementById("get-estimate-popup").style.display = "none";
                        });

                        const estimateCustomPopup = document.getElementById(
                          "estimate-popup-custom"
                        );
                        const locationPopup =
                          document.getElementById("location-popup");

                        const locationPopupCloseBtn = document.getElementById(
                          "estimate-custom-popup-close"
                        );

                        locationPopupCloseBtn.addEventListener(
                          "click",
                          function () {
                            estimateCustomPopup.style.display = "none";
                          }
                        );

                        // Attach event listeners after populating locations
                        document
                          .querySelectorAll(".quote-btn-custom")
                          .forEach((button) => {
                            button.addEventListener("click", function (event) {
                              const locationItem =
                                event.target.closest(".location-item");

                              if (locationItem) {
                                const clickedItemObj = {
                                  placeTitle:
                                    locationItem
                                      .querySelector(".locationitem_title")
                                      ?.textContent.trim() || null,
                                  placeAddress:
                                    locationItem
                                      .querySelector(".locationitem_address")
                                      ?.textContent.trim() || null,
                                  mobileNumber:
                                    locationItem
                                      .querySelector(".locationitem_phone")
                                      ?.textContent.trim() || null,
                                  websiteLink:
                                    locationItem
                                      .querySelector(".locationitem_link")
                                      ?.getAttribute("href") || null,
                                  locationKey:
                                    locationItem
                                      .querySelector(".seletced_location_key")
                                      ?.textContent.trim() || null,
                                  locationServiceminderKey:
                                    locationItem
                                      .querySelector(
                                        ".seletced_location_sm_key"
                                      )
                                      ?.textContent.trim() || null,
                                  locationId: locationItem.dataset.locationId || null,
                                  locationSlug: locationItem.dataset.locationSlug || null,
                                  locationZipcode: inputZip || null,
                                };

                                console.log(
                                  "clickedItemObj---",
                                  clickedItemObj
                                );

                                locationPopup.style.display = "none";
                                estimateCustomPopup.style.display = "flex";

                                populateGravityLocationFields(
                                  clickedItemObj.locationZipcode,
                                  {
                                    id: clickedItemObj.locationId,
                                    slug: clickedItemObj.locationSlug,
                                  }
                                );

                                // document.getElementById("zip-custom").value =
                                //   clickedItemObj.locationZipcode;
                                // document.getElementById("key-custom").value =
                                //   clickedItemObj.locationKey;
                                // document.getElementById("key-custom-sm").value =
                                //   clickedItemObj.locationServiceminderKey;
                                // document.getElementById("url-custom").value =
                                //   clickedItemObj.websiteLink;
                                setPopupPhoneLink(
                                  "est-phone-number-custom",
                                  clickedItemObj.mobileNumber
                                );
                                document.getElementById(
                                  "tel-href-custom"
                                ).href = `tel:${clickedItemObj.mobileNumber}`;

                                showGravityQuoteForms();
                              }
                            });
                          });
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
              document.getElementById("loader-wrapper").style.display = "none";

              console.error("Network error:", error);
              alert(
                "Failed to fetch ZIP codes. Please check your network connection."
              );
            });
        }
      }
    }
  });
});

document.querySelectorAll(".find-location-btn").forEach(function (button) {
  button.addEventListener("click", function () {
    var inputZip = this.closest(".location-container")
      .querySelector(".top-zipcode-input")
      .value.trim();

    if (inputZip === "") {
      document.getElementById("location-popup").style.display = "none";
      alert("Enter Zip or Postal Code");
      return;
    }

    const zipCode = inputZip;
    const radius = 60;
    const apiKey =
      "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII";

    // var locations = document.querySelectorAll(".single-location");
    var nearbyLocationFinalArr = [];
    // Clear previous popup content (if any)
    const popupContainer = document.getElementById(
      "location-popup-container"
    );
    popupContainer.innerHTML = ""; // Clear previous popup data
    const popupInnerStatic = document.getElementById(
      "location-popup-inner-static"
    );

    popupInnerStatic.style.display = "flex";

    // Reset form display settings at the start of each search
    showGravityQuoteForms();


    let matchFound = false;

    fetch(ajaxData.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "match_location_by_zip",
        nonce: ajaxData.match_location_nonce,
        zip_code: inputZip,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        console.log('data', data.data.location);
        if (data.data.matched && data.data.location) {
          const locations = data.data.location;

          populateGravityLocationFields(inputZip, locations);

          document.getElementById("popup-location_title").textContent = locations.title;
          document.getElementById("popup-location_address").textContent = locations.address;
          document.getElementById("popup-location_mobile").textContent = locations.phone;
          document.getElementById("popup-location_link").href = locations.website;
          document.getElementById("location-popup").style.display = "flex";

          // document.getElementById("zip").value = inputZip;
          // document.getElementById("key").value = locations.key;
          // document.getElementById("keySm").value = locations.sm_key;
          // document.getElementById("url").value = locations.website;
          setPopupPhoneLink("est-phone-number", locations.phone);
          document.getElementById("tel-href").href = "tel:" + locations.phone;
document.getElementById("get-estimate-popup").style.display = "none";
        } else {
          console.log("No direct zip match found.");
          const allLocations = data.data.locations;
          runFallbackSearch(zipCode, radius, apiKey, allLocations);
        }
      })
      .catch((err) => {
        console.error("AJAX error:", err);
      });
    function runFallbackSearch(zipCode, radius, apiKey, allLocations) {
      if (!matchFound) {
        //initialize loader
        document.getElementById("loader-wrapper").style.display = "flex";

        var matchedZipcodesArr = [];
        document.getElementById("location-popup").style.display = "none";
        console.log("No direct match found. Fetching nearby ZIP codes...");

        // Make the API request
        fetch(ajaxData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            action: "get_zip_codes_in_radius",
            nonce: ajaxData.zip_code_in_radius_nonce,
            zip_code: zipCode,
            radius: radius,
            api_key: apiKey,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              console.log("ZIP codes within the radius:", data.data.response);

              if (
                data.data.response.zip_codes &&
                data.data.response.zip_codes.length > 0
              ) {
                // Extract nearby ZIP codes
                const nearbyZips = data.data.response.zip_codes.map(
                  (item) => item.zip_code
                );
                console.log("Nearby ZIP Codes:", nearbyZips);


                allLocations.forEach(function (location) {
                  const zipcode = location.zipcode;
                  const additionalZipcodes = location.additional_zipcodes || [];

                  const allZips = [zipcode, ...additionalZipcodes.map(zip => zip.trim())];

                  const matchingZips = allZips.filter(zip => nearbyZips.includes(zip));

                  if (matchingZips.length > 0) {
                    console.log("Matching ZIP Codes:", matchingZips);

                    matchedZipcodesArr.push(...matchingZips);

                    nearbyLocationFinalArr.push({
                      placeTitle: location.title,
                      placeAddress: location.address,
                      mobileNumber: location.phone,
                      websiteLink: location.website,
                      locationKey: location.key,
                      locationServiceminderKey: location.sm_key,
                      locationId: location.id,
                      locationSlug: location.slug,
                      locationzipcode: zipcode,
                      matchedZipcode: matchingZips,
                    });
                  }
                });

                console.log(
                  "Final nearby locations array:",
                  nearbyLocationFinalArr
                );

                if (nearbyLocationFinalArr.length > 0) {
                  fetch(ajaxData.ajax_url, {
                    method: "POST",
                    headers: {
                      "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams({
                      action: "get_zip_codes_distance_in_miles",
                      input_zip: inputZip,
                      nearby_zips: JSON.stringify(matchedZipcodesArr), // Send as JSON string
                    }),
                  })
                    .then((response) => response.json())
                    .then((data) => {
                      //hide loader
                      document.getElementById("loader-wrapper").style.display =
                        "none";

                      if (!data.success) {
                        throw new Error(
                          data.data.message || "Failed to fetch distances"
                        );
                      }

                      console.log("Distance Data:", data.data);

                      const sortedZipCodes = data.data.map((item) => item.zip);

                      console.log("Sorted ZIP Codes:", sortedZipCodes);

                      // Reorder nearbyLocationFinalArr based on sorted zip codes
                      nearbyLocationFinalArr.sort((a, b) => {
                        // Find the first matching ZIP from sortedZipCodes in each location's matchedZipcode array
                        const indexA = sortedZipCodes.findIndex((zip) =>
                          a.matchedZipcode.includes(zip)
                        );
                        const indexB = sortedZipCodes.findIndex((zip) =>
                          b.matchedZipcode.includes(zip)
                        );

                        // If no match is found, set index to Infinity so unmatched locations go last
                        return (
                          (indexA === -1 ? Infinity : indexA) -
                          (indexB === -1 ? Infinity : indexB)
                        );
                      });

                      console.log(
                        "Sorted nearby locations:",
                        nearbyLocationFinalArr
                      );

                      // Now proceed with displaying the sorted locations
                      document.getElementById("location-popup").style.display =
                        "flex";
                      popupInnerStatic.style.display = "none"; // Hide static content

                      // Clear existing content before adding new locations
                      popupContainer.innerHTML = "";

                      // Add new content for each sorted location
                      nearbyLocationFinalArr.forEach((item) => {
                        const locationDiv = document.createElement("div");
                        locationDiv.classList.add("location-item");
                        locationDiv.dataset.locationId = item.locationId;
                        locationDiv.dataset.locationSlug = item.locationSlug;

                        locationDiv.innerHTML = `
        <h3 class="brxe-heading heading-style-h2 locationitem_title">${item.placeTitle}</h3>
        <h3 class="brxe-heading text-size-regular locationitem_address">${item.placeAddress}</h3>
        <h3 class="brxe-heading text-size-regular locationitem_phone">${item.mobileNumber}</h3>
        <div id="brxe-tfhrjk" class="brxe-block">
          <a href="${item.websiteLink}" class="brxe-div locationitem_link">
            <div id="brxe-xonhvx" class="brxe-text-basic">Visit Website</div>
          </a>
          <a class="brxe-div quote-btn-custom">
            <div class="brxe-text-basic"><span>Get a Free Estimate</span></div>
          </a>
          <p style="display:none;" class="seletced_location_key">${item.locationKey}</p>
          <p style="display:none;" class="seletced_location_sm_key">${item.locationServiceminderKey}</p>
          <p style="display:none;" class="location_zipcode">${item.locationzipcode}</p>
        </div>
      `;
                        popupContainer.appendChild(locationDiv);
                        document.getElementById("get-estimate-popup").style.display = "none";
                      });

                      const estimateCustomPopup = document.getElementById(
                        "estimate-popup-custom"
                      );
                      const locationPopup =
                        document.getElementById("location-popup");

                      const locationPopupCloseBtn = document.getElementById(
                        "estimate-custom-popup-close"
                      );

                      locationPopupCloseBtn.addEventListener(
                        "click",
                        function () {
                          estimateCustomPopup.style.display = "none";
                        }
                      );

                      // Attach event listeners after populating locations
                      document
                        .querySelectorAll(".quote-btn-custom")
                        .forEach((button) => {
                          button.addEventListener("click", function (event) {
                            const locationItem =
                              event.target.closest(".location-item");

                            if (locationItem) {
                              const clickedItemObj = {
                                placeTitle:
                                  locationItem
                                    .querySelector(".locationitem_title")
                                    ?.textContent.trim() || null,
                                placeAddress:
                                  locationItem
                                    .querySelector(".locationitem_address")
                                    ?.textContent.trim() || null,
                                mobileNumber:
                                  locationItem
                                    .querySelector(".locationitem_phone")
                                    ?.textContent.trim() || null,
                                websiteLink:
                                  locationItem
                                    .querySelector(".locationitem_link")
                                    ?.getAttribute("href") || null,
                                locationKey:
                                  locationItem
                                    .querySelector(".seletced_location_key")
                                    ?.textContent.trim() || null,
                                locationServiceminderKey:
                                  locationItem
                                    .querySelector(".seletced_location_sm_key")
                                    ?.textContent.trim() || null,
                                locationId: locationItem.dataset.locationId || null,
                                locationSlug: locationItem.dataset.locationSlug || null,
                                locationZipcode: inputZip || null,
                              };

                              console.log("clickedItemObj---", clickedItemObj);

                              locationPopup.style.display = "none";
                              estimateCustomPopup.style.display = "flex";

                              populateGravityLocationFields(
                                clickedItemObj.locationZipcode,
                                {
                                  id: clickedItemObj.locationId,
                                  slug: clickedItemObj.locationSlug,
                                }
                              );

                              // document.getElementById("zip-custom").value =
                              //   clickedItemObj.locationZipcode;
                              // document.getElementById("key-custom").value =
                              //   clickedItemObj.locationKey;
                              // document.getElementById("key-custom-sm").value =
                              //   clickedItemObj.locationServiceminderKey;
                              // document.getElementById("url-custom").value =
                              //   clickedItemObj.websiteLink;
                              setPopupPhoneLink(
                                "est-phone-number-custom",
                                clickedItemObj.mobileNumber
                              );
                              document.getElementById(
                                "tel-href-custom"
                              ).href = `tel:${clickedItemObj.mobileNumber}`;

                              showGravityQuoteForms();
                            }
                          });
                        });
                    })
                    .catch((error) => {
                      //hide loader
                      document.getElementById("loader-wrapper").style.display =
                        "none";

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
              document.getElementById("loader-wrapper").style.display = "none";

              console.error("Error fetching ZIP codes:", data.data.message);
              alert("Failed to fetch ZIP codes. Please try again.");
            }
          })
          .catch((error) => {
            //hide loader
            document.getElementById("loader-wrapper").style.display = "none";

            console.error("Network error:", error);
            alert(
              "Failed to fetch ZIP codes. Please check your network connection."
            );
          });
      }
    }
  });
});

document
  .getElementById("popup-location_close")
  .addEventListener("click", function () {
    document.getElementById("location-popup").style.display = "none";
    document.getElementById("get-estimate-popup").style.display = "none";
    document.querySelectorAll(".top-zipcode-input").forEach(function (input) {
      input.value = "";
    });
    resetGravityQuoteForms();
  });

const popupLocationClose2 = document.getElementById("popup-location_close2");

if (popupLocationClose2) {
  popupLocationClose2.addEventListener("click", function () {
    document.getElementById("location-popup").style.display = "none";
    document.getElementById("get-estimate-popup").style.display = "none";
    document.querySelectorAll(".top-zipcode-input").forEach(function (input) {
      input.value = "";
    });
  });
}

// Get estimate button function
const estimateBtn = document.getElementById("get-estimate-btn");

if (estimateBtn) {
  estimateBtn.addEventListener("click", function () {
    const popup = document.getElementById("get-estimate-popup");
    if(document.getElementById("brxe-fgxzrh").classList.contains('brx-open')) {
      document.getElementById("brxe-nagnqq").click();
    }
    if (popup) {
      popup.style.display = "flex";
    }
  });
}

const getEstimateBtn1 = document.getElementById("get-estimate-btn1");
const getEstimateBtn2 = document.getElementById("get-estimate-btn2");
const getEstimateBtnServiceSingle = document.getElementById(
  "service-detail-est-btn"
);

if (getEstimateBtn1) {
  getEstimateBtn1.addEventListener("click", function () {
    document.getElementById("get-estimate-popup").style.display = "flex";
  });
}

if (getEstimateBtn2) {
  getEstimateBtn2.addEventListener("click", function () {
    document.getElementById("get-estimate-popup").style.display = "flex";
  });
}

if (getEstimateBtnServiceSingle) {
  getEstimateBtnServiceSingle.addEventListener("click", function () {
    document.getElementById("get-estimate-popup").style.display = "flex";
  });
}

document
  .getElementById("get-estimate-close-btn")
  .addEventListener("click", function () {
    //console.log("close");
    document.getElementById("get-estimate-popup").style.display = "none";
  });
