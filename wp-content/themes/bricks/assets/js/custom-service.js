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

/**
 * Finds the locations nearest to a ZIP code for the search bars.
 *
 * Calls Koala Gravity Integration's `kgi_find_location` action, which makes
 * at most one cached zipcodeapi.com request with the server-side key.
 * Resolves (never rejects) to { status, message, locations }, where each
 * location uses the popup's item shape. `message` is the visitor-facing text
 * for statuses with no locations ("no location nearby", "please try again
 * later", invalid ZIP, too many searches).
 */
function koalaFindNearbyLocations(zipCode) {
  var ajaxUrl =
    (window.ajaxData && window.ajaxData.ajax_url) ||
    (window.koalaData && window.koalaData.ajax_url) ||
    "/wp-admin/admin-ajax.php";
  var fallbackMessage =
    "We're having trouble looking up your area right now. Please try again later.";

  return fetch(ajaxUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      action: "kgi_find_location",
      code: zipCode,
    }),
  })
    .then((response) => response.json())
    .then(function (data) {
      var locations = (data.locations || []).map(function (location) {
        return {
          placeTitle: location.title,
          placeAddress: location.address,
          mobileNumber: location.phone,
          websiteLink: location.website,
          locationKey: "",
          locationServiceminderKey: "",
          locationId: location.id,
          locationSlug: location.slug,
          locationzipcode: location.zipcode,
          matchedZipcode: [location.matched_code],
          distance: location.distance,
        };
      });

      return {
        status: data.status,
        // Matches have no message; anything else without one is a failure.
        message: data.message || (locations.length ? "" : fallbackMessage),
        locations: locations,
      };
    })
    .catch(function (error) {
      console.error("Location lookup failed:", error);

      return {
        status: "lookup_failed",
        message: fallbackMessage,
        locations: [],
      };
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
      // Force Hotjar recording for this flow specifically, regardless of the
      // general 1-in-100 sampling — the team wants full visibility into the
      // zip/location lookup search.
      if (window.koalaLoadHotjar) window.koalaLoadHotjar();

      var locationContainer = this.closest(".location-container");
      var inputZip = this.value.trim();
      // The nav ZIP lookup should surface only the single nearest location,
      // while other ZIP inputs keep showing the full ranked list.
      var isNavZipInput = this.id === "my-zipcode-input-nav";

      if (inputZip === "") {
        document.getElementById("location-popup").style.display = "none";
        alert("Enter Zip or Postal Code");
        return;
      }

      const zipCode = inputZip;

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
            runFallbackSearch(zipCode);
          }
        })
        .catch((err) => {
          console.error("AJAX error:", err);
        });

      function runFallbackSearch(zipCode) {
        if (!matchFound) {
          //initialize loader
          document.getElementById("loader-wrapper").style.display = "flex";

          document.getElementById("location-popup").style.display = "none";
          console.log("Fetching nearby ZIP codes...");

          // One cached server lookup (Koala Gravity Integration) returns the
          // nearby locations, closest first; no zipcodeapi.com key is used in
          // the browser.
          koalaFindNearbyLocations(zipCode).then(function (result) {
            //hide loader
            document.getElementById("loader-wrapper").style.display = "none";

            if (!result.locations.length) {
              alert(result.message);
              return;
            }

            nearbyLocationFinalArr = result.locations;

            // Now proceed with displaying the sorted locations
            document.getElementById(
              "location-popup"
            ).style.display = "flex";
            popupInnerStatic.style.display = "none"; // Hide static content

            // Clear existing content before adding new locations
            popupContainer.innerHTML = "";

            // Add new content for each sorted location. The nav ZIP
            // lookup is capped to the single nearest location.
            const locationsToRender = isNavZipInput
              ? nearbyLocationFinalArr.slice(0, 1)
              : nearbyLocationFinalArr;

            locationsToRender.forEach((item) => {
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

                    populateGravityLocationFields(
                      clickedItemObj.locationZipcode,
                      {
                        id: clickedItemObj.locationId,
                        slug: clickedItemObj.locationSlug,
                      }
                    );

                    // Open the Bricks estimate form popup (templateId
                    // 4865) via an existing trigger, exactly like the
                    // exact-match location button does. The old
                    // estimate-popup-custom has no form, and its
                    // tel-href-custom element was removed, which threw
                    // "Cannot set properties of null (setting 'href')"
                    // and left the fallback flow showing an empty popup.
                    showGravityQuoteForms();
                    document
                      .getElementById("national-nav-quote")
                      ?.click();
                  }
                });
              });
          });
        }
      }
    }
  });
});

document.querySelectorAll(".find-location-btn").forEach(function (button) {
  button.addEventListener("click", function () {
    // Force Hotjar recording for this flow specifically, regardless of the
    // general 1-in-100 sampling — the team wants full visibility into the
    // zip/location lookup search.
    if (window.koalaLoadHotjar) window.koalaLoadHotjar();

    var zipInputEl = this.closest(".location-container").querySelector(
      ".top-zipcode-input"
    );
    var inputZip = zipInputEl.value.trim();
    // The nav ZIP lookup should surface only the single nearest location,
    // while other ZIP inputs keep showing the full ranked list.
    var isNavZipInput = zipInputEl.id === "my-zipcode-input-nav";

    if (inputZip === "") {
      document.getElementById("location-popup").style.display = "none";
      alert("Enter Zip or Postal Code");
      return;
    }

    const zipCode = inputZip;

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
          runFallbackSearch(zipCode);
        }
      })
      .catch((err) => {
        console.error("AJAX error:", err);
      });
    function runFallbackSearch(zipCode) {
      if (!matchFound) {
        //initialize loader
        document.getElementById("loader-wrapper").style.display = "flex";

        document.getElementById("location-popup").style.display = "none";
        console.log("No direct match found. Fetching nearby ZIP codes...");

        // One cached server lookup (Koala Gravity Integration) returns the
        // nearby locations, closest first; no zipcodeapi.com key is used in
        // the browser.
        koalaFindNearbyLocations(zipCode).then(function (result) {
          //hide loader
          document.getElementById("loader-wrapper").style.display = "none";

          if (!result.locations.length) {
            alert(result.message);
            return;
          }

          nearbyLocationFinalArr = result.locations;

          // Now proceed with displaying the sorted locations
          document.getElementById("location-popup").style.display =
            "flex";
          popupInnerStatic.style.display = "none"; // Hide static content

          // Clear existing content before adding new locations
          popupContainer.innerHTML = "";

          // Add new content for each sorted location. The nav ZIP
          // lookup is capped to the single nearest location.
          const locationsToRender = isNavZipInput
            ? nearbyLocationFinalArr.slice(0, 1)
            : nearbyLocationFinalArr;

          locationsToRender.forEach((item) => {
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

                  populateGravityLocationFields(
                    clickedItemObj.locationZipcode,
                    {
                      id: clickedItemObj.locationId,
                      slug: clickedItemObj.locationSlug,
                    }
                  );

                  // Open the Bricks estimate form popup (templateId
                  // 4865) via an existing trigger, exactly like the
                  // exact-match location button does. The old
                  // estimate-popup-custom has no form, and its
                  // tel-href-custom element was removed, which threw
                  // "Cannot set properties of null (setting 'href')"
                  // and left the fallback flow showing an empty popup.
                  showGravityQuoteForms();
                  document
                    .getElementById("national-nav-quote")
                    ?.click();
                }
              });
            });
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
