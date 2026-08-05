/**
 * Estimate Form With Leads CRM integration
 */
// const locations = estimateData.zip_locations || [];
// console.log('Estimate Form Location:', locations);


function buildThankYouUrl(basePath) {
  const queryString = window.location.search; 
  let cleanPath = basePath.split('?')[0].replace(/\/$/, '');
  if (!cleanPath || cleanPath === "") {
    cleanPath = window.location.origin + window.location.pathname.replace(/\/$/, '');
  }
  const finalUrl = cleanPath + '/thank-you' + queryString;
  console.log("Redirecting to:", finalUrl);
  return finalUrl;
}

function getCookie(name) {
    return document.cookie.split('; ').find(row => row.startsWith(name + '='))?.split('=')[1] || '';
}

let utm_source   = getCookie('utm_source');
let utm_medium   = getCookie('utm_medium');
let utm_campaign = getCookie('utm_campaign');
const utmFields = {
  utm_source,
  utm_medium,
  utm_campaign,
};

document.addEventListener("DOMContentLoaded", function () {
  const estimateForm = document.getElementById("estimateForm-custom");
  const submitButton = estimateForm
    ? estimateForm.querySelector('button[type="submit"], input[type="submit"]')
    : "";

  if (!estimateForm) {
    return;
  }
  console.log(estimateData.page_id);

  // Ensure the form is bound only once
  if (!estimateForm.dataset.bound) {
    estimateForm.dataset.bound = "true";

    // Attach submit event listener
    estimateForm.addEventListener("submit", function (event) {
      event.preventDefault();
      // Disable button
      submitButton.disabled = true;

      // Create and insert spinner
      spinner = document.createElement("span");
      spinner.classList.add("loading-spinner");
      submitButton.parentNode.insertBefore(spinner, submitButton.nextSibling);

      const zip = document.getElementById("zip-custom").value.trim();
      // const locations = document.querySelectorAll(".single-location");
      const locations = estimateData.zip_locations || [];
      // console.log('Estimate Form Locations' , locations);
      let matchFound = false;
      let locationUrl = "";

      // Reusable function to submit form via AJAX
      function submitForm(key, zipValue, locationUrl, recaptchaToken) {
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_form",
        };
        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              window.location.href = buildThankYouUrl(locationUrl); 

              document.getElementById("estimateForm-custom").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSm(key, zipValue, locationUrl, recaptchaToken) {
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_sm_form_nonce,
          recaptcha_token: recaptchaToken,
          page_id: estimateData.page_id,
          action: "submit_estimate_sm_form",
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            try {
              const message = result?.data?.message;
              const statusCode = result?.data?.status_code;
              const resultCode = result?.data?.response?.ResultCode;
              // Ensure the response structure is correct
              if (
                resultCode === 0 ||
                (message == "Form submitted successfully." &&
                  statusCode === 200)
              ) {
                window.location.href = buildThankYouUrl(locationUrl); 
                document.getElementById("estimateForm").reset();

              } else {
                // Response code is not 200
                console.error(
                  "Invalid ResultCode:",
                  result?.data?.response?.ResultCode
                );
                showError("Submission failed. Please try again.");
              }
            } catch (error) {
              // Catch and log parsing or runtime errors
              console.error("An error occurred:", error);
              // Re-enable button
              submitButton.disabled = false;

              // Remove spinner
              if (spinner) {
                spinner.remove();
                spinner = null;
              }
              showError("An error occurred while processing your request.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSmWithHcp(
        key,
        sm_key,
        zipValue,
        locationUrl,
        recaptchaToken
      ) {
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          sm_key: sm_key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          recaptcha_token: recaptchaToken,
          action: "handle_both_submissions",
          page_id: estimateData.page_id,
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              window.location.href = buildThankYouUrl(locationUrl); 
              document.getElementById("estimateForm-custom").reset();

            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Function to display error message
      function showError(message) {
        const errorMessage = document.getElementById("errorMessage");
        errorMessage.textContent = message;
        errorMessage.style.display = "block";

        setTimeout(() => {
          errorMessage.style.display = "none";
        }, 3000);
      }

      // Check ZIP code match in locations
      // locations.forEach(function (location) {
      //   const zipcode = location.querySelector(".zipcode").textContent.trim();
      //   const additionalZipcodes = location
      //     .querySelector(".additional-zipcodes")
      //     .textContent.trim()
      //     .split(/\s*,\s*/);

      //   if (zipcode === zip || additionalZipcodes.includes(zip)) {
      //     const locationHcpKey = location
      //       .querySelector(".location-key")
      //       .textContent.trim();
      //     const locationSmKey = location
      //       .querySelector(".location-service-minder-key")
      //       .textContent.trim();
      //     locationUrl = location
      //       .querySelector(".place-link")
      //       .textContent.trim();

      //     if (locationHcpKey && locationSmKey) {
      //       submitFormSmWithHcp(
      //         locationHcpKey,
      //         locationSmKey,
      //         zip,
      //         locationUrl
      //       );
      //       console.log("Both keys found.");
      //     } else if (locationHcpKey && !locationSmKey) {
      //       submitForm(locationHcpKey, zip, locationUrl);
      //     } else if (locationSmKey && !locationHcpKey) {
      //       submitFormSm(locationSmKey, zip, locationUrl);
      //     } else {
      //       console.warn("Keys are empty for this ZIP code.");
      //     }

      //     matchFound = true;
      //   }
      // });

      grecaptcha.enterprise.ready(function () {
        grecaptcha.enterprise
          .execute("6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt", {
            action: "submit",
          })
          .then(function (token) {
            // Now you have the reCAPTCHA token
            // Pass it into your form submission logic

            locations.forEach(function (location) {
              const {
                zip: primaryZip,
                additional_zips,
                hcp_key,
                sm_key,
                url,
              } = location;

              if (primaryZip === zip || additional_zips.includes(zip)) {
                if (hcp_key && sm_key) {
                  submitFormSmWithHcp(hcp_key, sm_key, zip, url, token);
                } else if (hcp_key && !sm_key) {
                  submitForm(hcp_key, zip, url, token);
                } else if (sm_key && !hcp_key) {
                  submitFormSm(sm_key, zip, url, token);
                } else {
                  console.warn("Both keys are empty for this ZIP code.");
                }
                matchFound = true;
              }
            });

            // If no match found, submit with input field value (id="key") and ZIP code
            if (!matchFound) {
              const key = document.getElementById("key-custom").value;
              const keySm = document.getElementById("key-custom-sm").value;
              const locationUrl = document.getElementById("url-custom").value;

              if (key !== "" && keySm !== "") {
                submitFormSmWithHcp(key, keySm, zip, locationUrl, token);
                console.log("both found");
              } else if (key !== "" && keySm === "") {
                submitForm(key, zip, locationUrl, token);
              } else if (keySm !== "" && key === "") {
                submitFormSm(keySm, zip, locationUrl, token);
              } else {
                console.warn("Keys are empty.");
              }
            }
          })
          .catch(function () {
            showError(
              "reCAPTCHA verification failed. Please refresh and try again."
            );
          });
      });
    });

    // Phone input formatting
    document
      .getElementById("phone-custom")
      .addEventListener("input", function (e) {
        const x = e.target.value
          .replace(/\D/g, "")
          .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !x[2]
          ? x[1]
          : `(${x[1]}) ${x[2]}${x[3] ? `-${x[3]}` : ""}`;
      });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const estimateForm = document.getElementById("estimateForm");
  const submitButton = estimateForm
    ? estimateForm.querySelector('button[type="submit"], input[type="submit"]')
    : "";

  if (!estimateForm) {
    return;
  }

  // Ensure the form is bound only once
  if (!estimateForm.dataset.bound) {
    estimateForm.dataset.bound = "true";

    // Attach submit event listener
    estimateForm.addEventListener("submit", function (event) {
      event.preventDefault();

      // Disable button
      submitButton.disabled = true;

      // Create and insert spinner
      let spinner = document.createElement("span");
      spinner.classList.add("loading-spinner");
      submitButton.parentNode.insertBefore(spinner, submitButton.nextSibling);

      console.log("Submit click");

      const zip = document.getElementById("zip")?.value?.trim() || "";
      const locations = estimateData.zip_locations || [];
      let matchFound = false;
      let locationUrl = "";

      // Function to send Zapier webhook
      function sendToZapier(zapPayload) {
        try {
          const body = new URLSearchParams(zapPayload);
          fetch("https://hooks.zapier.com/hooks/catch/512909/u9h38yy/", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            },
            body: body.toString(),
            credentials: "same-origin",
            keepalive: true,
          }).catch(() => {}); // don’t block UX if it fails
        } catch (e) {
          console.warn("sendToZapier failed:", e);
        }
      }

      // Reusable function to submit form via AJAX
      function submitForm(key, zipValue, locationUrl, recaptchaToken) {
        const firstName = document.getElementById("fName")?.value || "";
        const lastName = document.getElementById("lName")?.value || "";
        const email = document.getElementById("email")?.value || "";
        const phone = document.getElementById("phone")?.value || "";
        const address1 = document.getElementById("address1")?.value || "";
        const address2 = document.getElementById("address2")?.value || "";
        const city = document.getElementById("city")?.value || "";
        const state = document.getElementById("state")?.value || "";
        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_form",
        };

        document.getElementById("errorMessage").style.display = "none";

        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            console.log("AJAX full response:", result);
            if (result?.data?.status_code === 201) {
              window.location.href = buildThankYouUrl(locationUrl); 


              const zapPayload = {
                action: "handle_zapier_webhook",
                nonce: estimateData.estimate_form_nonce,
                first_name: firstName,
                last_name: lastName,
                email,
                mobile_number: phone,
                address1,
                address2,
                city,
                state,
                zip: zipValue,
                ...utmFields,
                DoNotText: consent ? "1" : "0",
                cust_smsmarketingconsent: consentSmsMarketing ? "1" : "0",
                key,
                page_url: locationUrl,
              };

              sendToZapier(zapPayload);
              document.getElementById("estimateForm").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            submitButton.disabled = false;
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Reusable function to submit SM form via AJAX
      function submitFormSm(key, zipValue, locationUrl, recaptchaToken) {
        const firstName = document.getElementById("fName")?.value || "";
        const lastName = document.getElementById("lName")?.value || "";
        const email = document.getElementById("email")?.value || "";
        const phone = document.getElementById("phone")?.value || "";
        const address1 = document.getElementById("address1")?.value || "";
        const address2 = document.getElementById("address2")?.value || "";
        const city = document.getElementById("city")?.value || "";
        const state = document.getElementById("state")?.value || "";
        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_sm_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_sm_form",
          page_id: estimateData.page_id,
        };
        document.getElementById("errorMessage").style.display = "none";

        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            console.log("Full result:", result);
            try {
              const message = result?.data?.message;
              const statusCode = result?.data?.status_code;
              const resultCode = result?.data?.response?.ResultCode;

              if (
                resultCode === 0 ||
                (message == "Form submitted successfully." &&
                  statusCode === 200)
              ) {
                window.location.href = buildThankYouUrl(locationUrl); 


                const zapPayload = {
                  action: "handle_zapier_webhook",
                  nonce: estimateData.estimate_form_nonce,
                  first_name: firstName,
                  last_name: lastName,
                  email,
                  mobile_number: phone,
                  address1,
                  address2,
                  city,
                  state,
                  zip: zipValue,
                  ...utmFields,
                  DoNotText: consent ? "1" : "0",
                  key,
                  page_url: locationUrl,
                };

                sendToZapier(zapPayload);
                document.getElementById("estimateForm").reset();
              } else {
                console.error(
                  "Invalid ResultCode:",
                  result?.data?.response?.ResultCode
                );
                showError("Submission failed. Please try again.");
              }
            } catch (error) {
              console.error("An error occurred:", error);
              submitButton.disabled = false;
              if (spinner) {
                spinner.remove();
                spinner = null;
              }
              showError("An error occurred while processing your request.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            submitButton.disabled = false;
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Submit form for both HCP & SM
      function submitFormSmWithHcp(
        key,
        sm_key,
        zipValue,
        locationUrl,
        recaptchaToken
      ) {
        const firstName = document.getElementById("fName")?.value || "";
        const lastName = document.getElementById("lName")?.value || "";
        const email = document.getElementById("email")?.value || "";
        const phone = document.getElementById("phone")?.value || "";
        const address1 = document.getElementById("address1")?.value || "";
        const address2 = document.getElementById("address2")?.value || "";
        const city = document.getElementById("city")?.value || "";
        const state = document.getElementById("state")?.value || "";
        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          sm_key: sm_key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          recaptcha_token: recaptchaToken,
          action: "handle_both_submissions",
          page_id: estimateData.page_id,
        };

        document.getElementById("errorMessage").style.display = "none";

        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            console.log("AJAX full response:", result);

            if (result?.data?.status_code === 201) {
              window.location.href = buildThankYouUrl(locationUrl); 


              const zapPayload = {
                action: "handle_zapier_webhook",
                nonce: estimateData.estimate_form_nonce,
                first_name: firstName,
                last_name: lastName,
                email,
                mobile_number: phone,
                address1,
                address2,
                city,
                state,
                zip: zipValue,
                ...utmFields,
                DoNotText: consent ? "1" : "0",
                cust_smsmarketingconsent: consentSmsMarketing ? "1" : "0",
                key,
                page_url: locationUrl,
              };

              sendToZapier(zapPayload);
              document.getElementById("estimateForm").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            submitButton.disabled = false;
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Function to display error message
      function showError(message) {
        const errorMessage = document.getElementById("errorMessage");
        errorMessage.textContent = message;
        errorMessage.style.display = "block";
        setTimeout(() => {
          errorMessage.style.display = "none";
        }, 5000);
      }

      // reCAPTCHA
      grecaptcha.enterprise.ready(function () {
        grecaptcha.enterprise
          .execute("6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt", {
            action: "submit",
          })
          .then(function (token) {
            let matchFound = false;

            // Check if the body has one of the specified classes
            if (
              document.body.classList.contains("postid-66325") ||
              document.body.classList.contains("postid-66343")
            ) {
              const key = document.getElementById("key")?.value || "";
              const keySm = document.getElementById("keySm")?.value || "";
              locationUrl = window.location.href;

              if (key !== "" && keySm !== "") {
                submitFormSmWithHcp(key, keySm, zip, locationUrl, token);
                console.log("both found");
              } else if (key !== "" && keySm === "") {
                submitForm(key, zip, locationUrl, token);
              } else if (keySm !== "" && key === "") {
                submitFormSm(keySm, zip, locationUrl, token);
              } else {
                console.warn("Keys are empty.");
              }
            } else {
              // Original matching logic
              locations.forEach(function (location, index) {
                const {
                  zip: primaryZip,
                  additional_zips,
                  hcp_key,
                  sm_key,
                  url,
                } = location;

                // console.log(`Location #${index + 1}:`);
                // console.log("primaryZip:", primaryZip);
                // console.log("additional_zips:", additional_zips);
                // console.log("hcp_key:", hcp_key);
                // console.log("sm_key:", sm_key);
                // console.log("url:", url);

                if (primaryZip === zip || additional_zips.includes(zip)) {
                  if (hcp_key && sm_key) {
                    submitFormSmWithHcp(hcp_key, sm_key, zip, url, token);
                  } else if (hcp_key && !sm_key) {
                    submitForm(hcp_key, zip, url, token);
                  } else if (sm_key && !hcp_key) {
                    submitFormSm(sm_key, zip, url, token);
                  } else {
                    console.warn("Both keys are empty for this ZIP code.");
                  }
                  matchFound = true;
                }
              });

              console.log(matchFound);
              if (!matchFound) {
                const key = document.getElementById("key")?.value || "";
                const keySm = document.getElementById("keySm")?.value || "";
                const locationUrl =
                  document.getElementById("url")?.value || window.location.href;

                if (key !== "" && keySm !== "") {
                  submitFormSmWithHcp(key, keySm, zip, locationUrl, token);
                  console.log("both found");
                } else if (key !== "" && keySm === "") {
                  submitForm(key, zip, locationUrl, token);
                } else if (keySm !== "" && key === "") {
                  submitFormSm(keySm, zip, locationUrl, token);
                } else {
                  console.warn("Keys are empty.");
                }
              }
            }
          })
          .catch(function () {
            showError(
              "reCAPTCHA verification failed. Please refresh and try again."
            );
          });
      });

      // Phone input formatting handled by global formatter below
    });
  }
});

jQuery(window).on('load', function () {
  let customUrl = window.location.href;

  if (jQuery("#url").length) {
    let fieldVal = jQuery("#url").val() || jQuery("#url").attr("value");
    if (fieldVal) customUrl = fieldVal;
  }

  jQuery("#url-custom").attr("value", customUrl);
  if (jQuery("body").hasClass("single-location")) {
    jQuery("header #estimate-popup-custom").remove();

  }
});

document.addEventListener("DOMContentLoaded", function () {
  const estimateForm = document.getElementById("sidebar-form");
  const submitButton = estimateForm
    ? estimateForm.querySelector('button[type="submit"], input[type="submit"]')
    : "";

  if (!estimateForm) {
    return;
  }

  console.log(estimateData.page_id);

  // Ensure the form is bound only once
  if (!estimateForm.dataset.bound) {
    estimateForm.dataset.bound = "true";

    // Attach submit event listener
    estimateForm.addEventListener("submit", function (event) {
      event.preventDefault();
      // Disable button
      submitButton.disabled = true;

      // Create and insert spinner
      spinner = document.createElement("span");
      spinner.classList.add("loading-spinner");
      submitButton.parentNode.insertBefore(spinner, submitButton.nextSibling);
      const zip = document.getElementById("zip-custom").value.trim();
      // const locations = document.querySelectorAll(".single-location");
      const locations = estimateData.zip_locations || [];
      // console.log('Estimate Form Locations' , locations);
      let matchFound = false;
      let locationUrl = "";

      //K_code
      // Function to send Zapier webhook
      function sendToZapier(zapPayload) {
        try {
          const body = new URLSearchParams(zapPayload);
          fetch("https://hooks.zapier.com/hooks/catch/512909/u9h38yy/", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            },
            body: body.toString(),
            credentials: "same-origin",
            keepalive: true,
          }).catch(() => {}); // don’t block UX if it fails
        } catch (e) {
          console.warn("sendToZapier failed:", e);
        }
      }

      // Reusable function to submit form via AJAX
      function submitForm(key, zipValue, locationUrl, recaptchaToken) {
        const firstName = document.getElementById("fName-custom")?.value || "";
        const lastName = document.getElementById("lName-custom")?.value || "";
        const email = document.getElementById("email-custom")?.value || "";
        const phone = document.getElementById("phone-custom")?.value || "";
        const address1 =
          document.getElementById("address1-custom")?.value || "";
        const address2 =
          document.getElementById("address2-custom")?.value || "";
        const city = document.getElementById("city-custom")?.value || "";
        const state = document.getElementById("state-custom")?.value || "";

        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_form",
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              //K
              const zapPayload = {
                action: "handle_zapier_webhook",
                nonce: estimateData.estimate_form_nonce,
                // webhook: webhook_url, // only if you must

                first_name: firstName,
                last_name: lastName,
                email,
                mobile_number: phone,
                address1,
                address2,
                city,
                state,
                zip: zipValue,
                ...utmFields,

                DoNotText: consent ? "1" : "0",
                cust_smsmarketingconsent: consentSmsMarketing ? "1" : "0",
                key,
                page_url: locationUrl,
              };
              sendToZapier(zapPayload); // fire the server-side webhook
              window.location.href = buildThankYouUrl(locationUrl); 

              document.getElementById("sidebar-form").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSm(key, zipValue, locationUrl, recaptchaToken) {
        const firstName = document.getElementById("fName-custom")?.value || "";
        const lastName = document.getElementById("lName-custom")?.value || "";
        const email = document.getElementById("email-custom")?.value || "";
        const phone = document.getElementById("phone-custom")?.value || "";
        const address1 =
          document.getElementById("address1-custom")?.value || "";
        const address2 =
          document.getElementById("address2-custom")?.value || "";
        const city = document.getElementById("city-custom")?.value || "";
        const state = document.getElementById("state-custom")?.value || "";

        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_sm_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_sm_form",
          page_id: estimateData.page_id,
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            try {
              const message = result?.data?.message;
              const statusCode = result?.data?.status_code;
              const resultCode = result?.data?.response?.ResultCode;
              //Ensure the response structure is correct
              if (
                resultCode === 0 ||
                (message == "Form submitted successfully." &&
                  statusCode === 200)
              ) {
                //K

                const zapPayload = {
                  action: "handle_zapier_webhook",
                  nonce: estimateData.estimate_form_nonce,
                  // webhook: webhook_url, // only if you must

                  first_name: firstName,
                  last_name: lastName,
                  email,
                  mobile_number: phone,
                  address1,
                  address2,
                  city,
                  state,
                  zip: zipValue,
                  ...utmFields,

                  DoNotText: consent ? "1" : "0",
                  key,
                  page_url: locationUrl,
                };
                sendToZapier(zapPayload); // fire the server-side webhook
                document.getElementById("sidebar-form").reset();
                window.location.href = buildThankYouUrl(locationUrl); 

              }
            } catch (error) {
              // Catch and log parsing or runtime errors
              console.error("An error occurred:", error);
              // Re-enable button
              submitButton.disabled = false;

              // Remove spinner
              if (spinner) {
                spinner.remove();
                spinner = null;
              }
              showError("An error occurred while processing your request.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSmWithHcp(
        key,
        sm_key,
        zipValue,
        locationUrl,
        recaptchaToken
      ) {
        const firstName = document.getElementById("fName-custom")?.value || "";
        const lastName = document.getElementById("lName-custom")?.value || "";
        const email = document.getElementById("email-custom")?.value || "";
        const phone = document.getElementById("phone-custom")?.value || "";
        const address1 =
          document.getElementById("address1-custom")?.value || "";
        const address2 =
          document.getElementById("address2-custom")?.value || "";
        const city = document.getElementById("city-custom")?.value || "";
        const state = document.getElementById("state-custom")?.value || "";

        const checkbox = estimateForm.querySelector('input[name="consent"]');
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent_sms_marketing = estimateForm.querySelector('input[name="consent-sms-marketing"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const consentSmsMarketing = !!(consent_sms_marketing && consent_sms_marketing.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          cust_smsmarketingconsent: consentSmsMarketing,
          zip: zipValue,
          key: key,
          sm_key: sm_key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          recaptcha_token: recaptchaToken,
          action: "handle_both_submissions",
          page_id: estimateData.page_id,
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              //K
              const zapPayload = {
                action: "handle_zapier_webhook",
                nonce: estimateData.estimate_form_nonce,
                // webhook: webhook_url, // only if you must

                first_name: firstName,
                last_name: lastName,
                email,
                mobile_number: phone,
                address1,
                address2,
                city,
                state,
                zip: zipValue,
                ...utmFields,

                DoNotText: consent ? "1" : "0",
                cust_smsmarketingconsent: consentSmsMarketing ? "1" : "0",
                key,
                page_url: locationUrl,
              };
              sendToZapier(zapPayload); // fire the server-side webhook
              window.location.href = buildThankYouUrl(locationUrl); 

              document.getElementById("sidebar-form").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Function to display error message
      function showError(message) {
        const errorMessage = document.getElementById("errorMessage-custom");
        errorMessage.textContent = message;
        errorMessage.style.display = "block";

        setTimeout(() => {
          errorMessage.style.display = "none";
        }, 3000);
      }

      grecaptcha.enterprise.ready(function () {
        grecaptcha.enterprise
          .execute("6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt", {
            action: "submit",
          })
          .then(function (token) {
            // Now you have the reCAPTCHA token
            // Pass it into your form submission logic

            // START: MODIFICATION
            // Check if the body has one of the specified classes
            if (
              document.body.classList.contains("postid-66325") ||
              document.body.classList.contains("postid-66343")
            ) {
              // If it does, skip the matching logic and go straight to the fallback
              const key = document.getElementById("key-custom").value;
              const keySm = document.getElementById("key-custom-sm").value;
              locationUrl = document.getElementById("url-custom").value;
              locationUrl = window.location.href;
              console.log(locationUrl, window.location.href);

              if (key !== "" && keySm !== "") {
                submitFormSmWithHcp(key, keySm, zip, locationUrl, token);
                console.log("both found");
              } else if (key !== "" && keySm === "") {
                submitForm(key, zip, locationUrl, token);
              } else if (keySm !== "" && key === "") {
                submitFormSm(keySm, zip, locationUrl, token);
              } else {
                console.warn("Keys are empty.");
              }
            } else {
              // If the classes are not present, run the original matching logic
              locations.forEach(function (location) {
                const {
                  zip: primaryZip,
                  additional_zips,
                  hcp_key,
                  sm_key,
                  url,
                } = location;

                if (primaryZip === zip || additional_zips.includes(zip)) {
                  if (hcp_key && sm_key) {
                    submitFormSmWithHcp(hcp_key, sm_key, zip, url, token);
                  } else if (hcp_key && !sm_key) {
                    submitForm(hcp_key, zip, url, token);
                  } else if (sm_key && !hcp_key) {
                    submitFormSm(sm_key, zip, url, token);
                  } else {
                    console.warn("Both keys are empty for this ZIP code.");
                  }
                  matchFound = true;
                }
              });

              // If no match found, submit with input field value (id="key") and ZIP code
              if (!matchFound) {
                const key = document.getElementById("key-custom").value;
                const keySm = document.getElementById("key-custom-sm").value;
                const locationUrl = document.getElementById("url-custom").value;
                // locationUrl = document.getElementById("url-custom").value;
                console.log('here',locationUrl, window.location.href);

                if (key !== "" && keySm !== "") {
                  submitFormSmWithHcp(key, keySm, zip, locationUrl, token);
                  console.log("both found");
                } else if (key !== "" && keySm === "") {
                  submitForm(key, zip, locationUrl, token);
                } else if (keySm !== "" && key === "") {
                  submitFormSm(keySm, zip, locationUrl, token);
                } else {
                  console.warn("Keys are empty.");
                }
              }
            }
            // END: MODIFICATION
          })
          .catch(function () {
            showError(
              "reCAPTCHA verification failed. Please refresh and try again."
            );
          });
      });
    });

    // Phone input formatting
    document
      .getElementById("phone-custom")
      .addEventListener("input", function (e) {
        const x = e.target.value
          .replace(/\D/g, "")
          .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !x[2]
          ? x[1]
          : `(${x[1]}) ${x[2]}${x[3] ? `-${x[3]}` : ""}`;
      });
  }
});


// Global Phone Formatter (Handles Sidebar & Popups)
document.addEventListener("input", function (e) {
  // Target only our specific phone fields
  if (e.target && (e.target.id === "phone" || e.target.id === "phone-custom")) {
    let input = e.target.value.replace(/\D/g, "");
    if (input.startsWith("1")) input = input.substring(1);
    input = input.substring(0, 10);

    const areaCode = input.substring(0, 3);
    const middle = input.substring(3, 6);
    const last = input.substring(6, 10);

    let formatted = "";
    if (input.length === 0) {
      formatted = "";
    } else if (input.length <= 3) {
      formatted = `(${areaCode}`;
    } else if (input.length <= 6) {
      formatted = `(${areaCode}) ${middle}`;
    } else {
      formatted = `(${areaCode}) ${middle}-${last}`;
    }

    e.target.value = formatted;
  }
});