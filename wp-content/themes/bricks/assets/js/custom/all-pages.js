$ = jQuery;
document.addEventListener("DOMContentLoaded", function () {
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

  function setHrefForAll(elementId, href) {
    document.querySelectorAll("#" + elementId).forEach(function (element) {
      element.href = href;
    });
  }

  // reCAPTCHA loader removed — loaded via functions.php wp_enqueue_script to avoid duplicate loading

  // Queue NiceJob during page setup, but do not add its SDK to the document
  // until the visitor interacts with the page.
  let niceJobInteractionOccurred = false;
  let niceJobScriptQueued = false;
  const niceJobInteractionEvents = ["pointerdown", "keydown", "touchstart", "wheel"];

  function markNiceJobInteraction() {
    niceJobInteractionOccurred = true;
    niceJobInteractionEvents.forEach((eventName) => {
      window.removeEventListener(eventName, markNiceJobInteraction);
    });
  }

  niceJobInteractionEvents.forEach((eventName) => {
    window.addEventListener(eventName, markNiceJobInteraction, {
      once: true,
      passive: true,
    });
  });

  function loadNiceJobAfterInteraction(script) {
    if (
      niceJobScriptQueued ||
      document.querySelector('script[src*="cdn.nicejob.co"]')
    ) {
      return;
    }

    niceJobScriptQueued = true;

    const appendNiceJobScript = function () {
      // Allow the caller to attach its load and error handlers first.
      queueMicrotask(function () {
        if (!document.querySelector('script[src*="cdn.nicejob.co"]')) {
          document.body.appendChild(script);
        }
      });
    };

    if (niceJobInteractionOccurred) {
      appendNiceJobScript();
      return;
    }

    const loadOnInteraction = function () {
      niceJobInteractionEvents.forEach((eventName) => {
        window.removeEventListener(eventName, loadOnInteraction);
      });
      appendNiceJobScript();
    };

    niceJobInteractionEvents.forEach((eventName) => {
      window.addEventListener(eventName, loadOnInteraction, {
        once: true,
        passive: true,
      });
    });
  }


  var siteUrl = document.location.origin;
  console.log("siteUrl--", siteUrl);
  var currentUrl = document.location.href;

  // const smKey = sessionStorage.getItem("location_sm_key");
  // const hcpKey = sessionStorage.getItem("location_key");
  //console.log("smKey---", smKey);

  const locationInfo = sessionStorage.getItem("location_info");
  var locationUrl = sessionStorage.getItem("location_url");

  const wkLink = JSON.parse(sessionStorage.getItem("wk_link")) || [];
  const wrLink = JSON.parse(sessionStorage.getItem("wr_link")) || [];
  const hoLink = JSON.parse(sessionStorage.getItem("ho_link")) || [];
  const blogLink = JSON.parse(sessionStorage.getItem("blog_link")) || [];

  const locationNavTextUs = document.getElementById("nav-top-text-us");
  const locRegion = sessionStorage.getItem("location_region");
  const fbLink = sessionStorage.getItem("fb_link");
  const instaLink = sessionStorage.getItem("insta_link");
  const linkedinLink = sessionStorage.getItem("linkedin_link");
  const ytLink = sessionStorage.getItem("yt_link");
  const navText = sessionStorage.getItem("nav_top_text");
  const navTopLink = sessionStorage.getItem("nav_top_link");

  var changedHomeUrl = sessionStorage.getItem("location_url") || siteUrl;

  const elementShow = document.getElementById("location-nav-box");
  const elementDropDownShow = document.getElementById("custom-service");
  const elementHide = document.getElementById("default-nav-box");
  const elementDropDownHide = document.getElementById("main-nav-services");
  const elementFooterHide = document.getElementById("main-ft-services");
  const elementFooterShow = document.getElementById("custom-service-footer");
  const elementMainWidget = document.getElementById("main-page-widget");
  const elementLocalWidget = document.getElementById("local-page-widget");
  const elementMainStoriesWidget = document.getElementById(
    "main-page-stories-widget"
  );
  const elementLocalStoriesWidget = document.getElementById(
    "local-page-stories-widget"
  );
  const locationNavLink = document.getElementById("nav-location");
  const locationFooterLink = document.getElementById("ft-location");
  const privacyFooterLink = document.getElementById("ft-privacy-policy");
  const termsFooterLink = document.getElementById("ft-terms-and-conditions");
  const cta = document.getElementById("cta");
  const ctaAlt = document.getElementById("cta-alt");
  const ctaAlt2 = document.getElementById("cta-alt2");
  const ctaQuote = document.getElementById("cta-quote");
  const nationalNavQuote = document.getElementById("national-nav-quote");
  const localNavQuote = document.getElementById("get-estimate-btn");
  const nationalEstimateBtn = document.getElementById(
    "national_estimate-btn"
  );
  const nationalEstimateFooterBtn = document.getElementById("brxe-pcgfpe");
  const localEstimateBtn = document.getElementById("get-estimate-btn1");
  const localEstimateBtn1 = document.getElementById("get-estimate-btn2");
  const whyNavKoalaLink = document.getElementById("why-koala-link");
  const whyNavReinsulateLink = document.getElementById("why-reinsulate-link");
  const homeownerNavLink = document.getElementById("homeowner-link");
  const blogFooterLink = document.getElementById("blog-link-footer");
  const blogNavLink = document.getElementById("blog-link-nav");
  const whyFooterKoalaLink = document.getElementById("why-koala-link-footer");
  const whyFooterReinsulateLink = document.getElementById(
    "why-reinsulate-link-footer"
  );
  const homeownerFooterLink = document.getElementById(
    "homeowner-link-footer"
  );
  const homeownerLocalHomepageLink = document.getElementById(
    "local-home-savings-btn"
  );
  const fbFooter = document.getElementById("fb-link");
  const instaFooter = document.getElementById("insta-link");
  const linkedinFooter = document.getElementById("linkedin-link");
  const ytFooter = document.getElementById("yt-link");
  const usaNav = document.getElementById("nav-us");
  const usFooter = document.getElementById("footer-us");
  const usSlider = document.getElementById("usa-slider-why-koala");
  const usOurServices = document.getElementById("usa-our-services");
  const usOurServicesSubnav = document.getElementById("us-services-sub-nav");
  const areasServedNavLink = document.getElementById("areas-served-navlink");

  const franchiseNavLink = document.getElementById("franchise-nav-link");
  const nationalPartnershipsFooterLink = document.getElementById(
    "ft-national-partnerships"
  );
  const faqNavLink = document.getElementById("faq-nav-link");
  const testimonialsNavLink = document.getElementById(
    "testimonials-nav-link"
  );
  const franchiseFooterLink = document.getElementById("franchise-ft-link");
  const faqFooterLink = document.getElementById("faq-ft-link");
  const testimonialsFooterLink = document.getElementById(
    "testimonials-ft-link"
  );

  if (locationNavTextUs) {
    if (navText) {
      locationNavTextUs.textContent = navText;
    } else {
      locationNavTextUs.textContent =
        "";
    }
    if (navTopLink) {
      const navTopLinkAnchor = locationNavTextUs.closest("a");
      if (navTopLinkAnchor) {
        navTopLinkAnchor.href = navTopLink;
      }
    }
  }

  const googleReviewWrap = document.getElementById(
    "google-review-shortcode-wrapper"
  );
  const localPageWidget = document.getElementById("local-page-widget");
  const localPageStoriesWidget = document.getElementById(
    "local-page-stories-widget"
  );

  // Initial hide handled by koala-critical-init CSS in <head> — no JS needed here

  // Show the main nav immediately (may be hidden by Bricks visibility conditions)
  if (usaNav) {
    usaNav.style.display = "block";
    usaNav.style.visibility = "visible";
  }

  // Get the current URL pathname
  const pathname = window.location.pathname;
  console.log(pathname);

  // Split the URL into segments and filter out empty segments
  const segments = pathname.split("/").filter(Boolean);

  // Check if the URL contains a location slug
  if (segments.length > 0) {
    const locationSlug = segments[0]; // Assume the first segment is the location slug
    console.log(locationSlug);

    // Fetch location data from the REST API
    fetch(`/wp-json/custom/v1/location-data/${locationSlug}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Location data not found");
        }
        return response.json();
      })
      .then((data) => {
        sessionStorage.setItem("location_info", "true");
        if (data) {
          setHrefForAll("why-koala-link", `${data.url}/why-koala`);
          setHrefForAll("why-koala-link-footer", `${data.url}/why-koala`);
          setHrefForAll("why-reinsulate-link", `${data.url}/why-reinsulate`);
          setHrefForAll("why-reinsulate-link-footer", `${data.url}/why-reinsulate`);
          setHrefForAll("homeowner-link", `${data.url}/homeowner-incentives`);
          setHrefForAll("homeowner-link-footer", `${data.url}/homeowner-incentives`);
          if (currentUrl !== siteUrl + "/locations") {
            setHrefForAll("nav-home", data.url);
            setHrefForAll("ft-home", data.url);
          }
          if (locationNavTextUs) {
            locationNavTextUs.href = `${data.url}/homeowner-incentives`;
          }
          if (homeownerLocalHomepageLink) {
            homeownerLocalHomepageLink.href = `${data.url}/homeowner-incentives`;
          }
          setHrefForAll("ft-terms-and-conditions", `${data.url}/terms-and-conditions`);
          setHrefForAll("ft-privacy-policy", `${data.url}/privacy-policy`);
          franchiseNavLink.style.display = "none";
          franchiseFooterLink.style.display = "none";
          nationalPartnershipsFooterLink.style.display = "none";
          setHrefForAll("faq-nav-link", `${data.url}/faq`);
          setHrefForAll("testimonials-nav-link", `${data.url}/testimonials`);
          setHrefForAll("faq-ft-link", `${data.url}/faq`);
          setHrefForAll("testimonials-ft-link", `${data.url}/testimonials`);
        }

        if (data.name) {
          document.getElementById("loc-name").textContent = data.name;
          sessionStorage.setItem("location_name", data.name);
        } else {
          sessionStorage.setItem("location_name", "");
        }

        if (data.address) {
          document.getElementById("footer-address").textContent =
            data.address;
          sessionStorage.setItem("location_address", data.address);
        } else {
          sessionStorage.setItem("location_address", "");
        }

        if (data.rocNumber) {
          const rocElement = document.getElementById("ft-roc-number");
          if (rocElement) {
            rocElement.style.display = "block";
            rocElement.textContent = data.rocNumber;
          }

          sessionStorage.setItem("location_roc", data.rocNumber);
        } else {
          const rocElement = document.getElementById("ft-roc-number");
          if (rocElement) {
            rocElement.style.display = "none"; // Use "none" to hide the element
            rocElement.textContent = ""; // Clear the text content just in case
          }

          sessionStorage.setItem("location_roc", ""); // Clear the session storage
        }

        if (data.url) {
          sessionStorage.setItem("location_url", data.url);
        } else {
          sessionStorage.setItem("location_url", "");
        }

        const estimatePhoneNumber = data.phone1 || "(833)-335-6252";
        setPopupPhoneLink("est-phone-number", estimatePhoneNumber);

        if (data.phone1) {
          setHrefForAll("phone1-wrapper", "tel:" + data.phone1);
          setHrefForAll("tel-href", "tel:" + data.phone1);
          document.querySelectorAll("#loc-phone-number").forEach(function (element) {
            element.textContent = data.phone1;
          });
          document.querySelectorAll("#footer-number").forEach(function (element) {
            element.textContent = data.phone1;
            element.href = "tel:" + data.phone1;
          });
          sessionStorage.setItem("location_phone_number", data.phone1);

          // Re-trigger CallRail swap after phone numbers are set by AJAX.
          // swap.js may still be loading, so retry until CallTrk is available.
          (function retrySwap(attempts) {
            if (typeof CallTrk !== 'undefined' && typeof CallTrk.swap === 'function') {
              CallTrk.swap();
            } else if (attempts > 0) {
              setTimeout(function () { retrySwap(attempts - 1); }, 300);
            }
          })(10);
        } else {
          sessionStorage.setItem("location_phone_number", "");
          document.getElementById("phone1-wrapper").href =
            "tel:" + "(833)-335-6252";
          document.getElementById("loc-phone-number").innerHTML =
            "(833)-335-6252";
        }

        if (data.phone2) {
          document.getElementById("phone2-wrapper").href =
            "tel:" + data.phone2;
          document.getElementById("phone2-wrapper").style.display = "flex";
          document.getElementById("loc-phone-number2").innerHTML =
            data.phone2;
          sessionStorage.setItem("location_phone_number2", data.phone2);
        } else {
          sessionStorage.setItem("location_phone_number2", "");
          document.getElementById("phone2-wrapper").style.display = "none";
          document.getElementById("loc-phone-number2").innerHTML = "";
        }

        if (data.nicejobId) {
          if (googleReviewWrap && localPageWidget && localPageStoriesWidget) {
            googleReviewWrap.style.display = "none";
            googleReviewWrap.style.visibility = "hidden";
            localPageWidget.style.display = "block";
            localPageWidget.style.visibility = "visible";
            localPageStoriesWidget.style.display = "block";
            localPageStoriesWidget.style.visibility = "visible";
          }

          sessionStorage.setItem("location_nicejob_id", data.nicejobId);

          if (!document.querySelector('script[src*="cdn.nicejob.co"]')) {
            const script = document.createElement("script");
            script.src = "https://cdn.nicejob.co/js/sdk.min.js?id=" + data.nicejobId;
            script.async = true; // Ensure the script loads asynchronously
            loadNiceJobAfterInteraction(script);
            script.onload = function () {
              console.log("NiceJob SDK loaded successfully.");
            };
            script.onerror = function () {
              console.error("Failed to load NiceJob SDK.");
            };
          } // end duplicate guard
        } else {
          sessionStorage.setItem("location_nicejob_id", "");
        }

        // if (data.hcpKey) {
        //   document.getElementById("key").value = data.hcpKey;
        //   document.querySelector('#estimateForm #key').value = data.hcpKey;
        //   document.getElementById("url").value = data.url;
        //   sessionStorage.setItem("location_key", data.hcpKey);
        // } else {
        //   sessionStorage.setItem("location_key", "");
        // }

        // if (data.smKey) {
        //   document.getElementById("keySm").value = data.smKey;
        //   document.querySelector('#estimateForm #keySm').value = data.smKey;
        //   document.getElementById("url").value = data.url;
        //   sessionStorage.setItem("location_sm_key", data.smKey);
        // } else {
        //   sessionStorage.setItem("location_sm_key", "");
        // }

        if (data.grShortcode) {
          if (googleReviewWrap && localPageWidget && localPageStoriesWidget) {
            googleReviewWrap.style.display = "flex";
            googleReviewWrap.style.visibility = "visible";
            localPageWidget.style.display = "none";
            localPageStoriesWidget.style.display = "none";
          }
          sessionStorage.setItem("google_review_shortcode", data.grShortcode);
        } else {
          sessionStorage.setItem("google_review_shortcode", "");
        }

        if (data.nicejobId === "" && data.grShortcode === "") {
          const localWidgets = document.querySelectorAll(".nicejob-stories-widget");
          localWidgets.forEach((widget) => {
            widget.remove();
            console.log(".nicejob-stories-widget removed before loading NiceJob SDK");
          });
          const elementMainWidget =
            document.getElementById("main-page-widget");
          const elementMainStoriesWidget = document.getElementById(
            "main-page-stories-widget"
          );
          if (elementMainWidget) {
            elementMainWidget.style.display = "block";
            elementMainWidget.style.visibility = "visible";
          }
          if (elementMainStoriesWidget) {
            elementMainStoriesWidget.style.display = "block";
            elementMainStoriesWidget.style.visibility = "visible";
          }

          if (!document.querySelector('script[src*="cdn.nicejob.co"]')) {
          const script = document.createElement("script");
          script.src =
            "https://cdn.nicejob.co/js/sdk.min.js?id=5343641076236288";
          script.async = true; // Ensure the script loads asynchronously
          loadNiceJobAfterInteraction(script);

          script.onload = function () {
            console.log("NiceJob SDK loaded successfully.");
            // No need to manually call NiceJob.showRecommendation(), let SDK handle it
             const schemaObserver = new MutationObserver((mutations, observer) => {
  let removed = false;

  const ldSchemas = document.querySelectorAll('script[type="application/ld+json"]');
  ldSchemas.forEach((scriptTag) => {
    try {
      const json = JSON.parse(scriptTag.innerText);
      if (json['@type'] === 'Review') {
        if (scriptTag.parentNode) {
          scriptTag.parentNode.removeChild(scriptTag); // more reliable than .remove()
          console.log('Removed NiceJob review schema.');
          removed = true;
        }
      }
    } catch (e) {
      // Ignore parse errors
    }
  });

  if (removed) {
    observer.disconnect(); // Stop after first actual removal
  }
});

schemaObserver.observe(document.documentElement, {
  childList: true,
  subtree: true
});

          };

          script.onerror = function () {
            console.error("Failed to load NiceJob SDK.");
          };
          } // end duplicate guard
        }

        if (data.fbLink) {
          fbFooter.href = `${data.fbLink}`;
          sessionStorage.setItem("fb_link", data.fbLink);
        } else {
          sessionStorage.setItem("fb_link", "");
          fbFooter.style.display = "none";
        }

        if (data.instaLink) {
          instaFooter.href = `${data.instaLink}`;
          sessionStorage.setItem("insta_link", data.instaLink);
        } else {
          sessionStorage.setItem("insta_link", "");
          instaFooter.style.display = "none";
        }

        if (data.linkedinLink) {
          linkedinFooter.href = `${data.linkedinLink}`;
          sessionStorage.setItem("linkedin_link", data.linkedinLink);
        } else {
          sessionStorage.setItem("linkedin_link", "");
          linkedinFooter.style.display = "none";
        }

        if (data.ytLink) {
          ytFooter.href = `${data.linkedinLink}`;
          sessionStorage.setItem("yt_link", data.ytLink);
        } else {
          sessionStorage.setItem("yt_link", "");
          ytFooter.style.display = "none";
        }

        // if (data.scriptInHead) {
        //   sessionStorage.setItem(
        //     "location_before_head_tag",
        //     data.scriptInHead
        //   );
        //   console.log(data.scriptInHead);
        // } else {
        //   sessionStorage.setItem("location_before_head_tag", "");
        // }

        if (data.scriptInHead) {
          sessionStorage.setItem(
             "location_before_head_tag",
             data.scriptInHead
          );

          // Only inject if PHP did not already output this script in <head>
          if (!window.koalaHeadScriptOutput) {
            let head =
              document.head || document.getElementsByTagName("head")[0];
            let wrapper = document.createElement("div");
            wrapper.innerHTML = data.scriptInHead;

            // Append each script individually to head
            Array.from(wrapper.children).forEach((element) => {
              if (element.tagName === "SCRIPT") {
                let script = document.createElement("script");
                if (element.src) {
                  script.src = element.src;
                  script.async = element.async;
                } else {
                  script.textContent = element.textContent;
                }
                head.appendChild(script);
              } else {
                head.appendChild(element);
              }
            });
          }
        }

        // if (data.scriptBeforeHead) {
        //   sessionStorage.setItem(
        //     "location_before_body_tag",
        //     data.scriptBeforeHead
        //   );
        // } else {
        //   sessionStorage.setItem("location_before_body_tag", "");
        // }

        // if (data.scriptInBody) {
        //   sessionStorage.setItem(
        //     "location_after_body_tag",
        //     data.scriptInBody
        //   );
        // } else {
        //   sessionStorage.setItem("location_after_body_tag", "");
        // }

        if (data.scriptInBody) {
          // Store in sessionStorage (if needed)
          sessionStorage.setItem(
            "location_after_body_tag",
            data.scriptInBody
          );

          // Only inject if PHP did not already output this script at body open
          if (!window.koalaBodyScriptOutput) {
          // Get the body element
          let body = document.body;

          // Create a wrapper to hold the content
          let wrapper = document.createElement("div");
          wrapper.innerHTML = data.scriptInBody;

          // Append each child element from wrapper to the body
          Array.from(wrapper.children).forEach((element) => {
            if (element.tagName === "SCRIPT") {
              // For script elements, create a new script and append it to body
              let script = document.createElement("script");
              if (element.src) {
                script.src = element.src;
                script.async = element.async;
              } else {
                script.textContent = element.textContent;
              }
              body.appendChild(script);
            } else {
              // For other elements like noscript, append directly to the body
              body.appendChild(element);
            }
          });
          }
        } else {
          // If no scriptBeforeHead, store empty value (or handle as needed)
          sessionStorage.setItem("location_after_body_tag", "");
        }

        if (data.navText) {
          document.getElementById("nav-top-text-us").textContent =
            data.navText;
          sessionStorage.setItem("nav_top_text", data.navText);
        } else {
          sessionStorage.setItem("nav_top_text", "");
        }

        if (data.navTopLink) {
          const navTopLinkEl = document.getElementById("nav-top-text-us");
          if (navTopLinkEl) {
            const navTopLinkAnchor = navTopLinkEl.closest("a");
            if (navTopLinkAnchor) {
              navTopLinkAnchor.href = data.navTopLink;
            }
          }
          sessionStorage.setItem("nav_top_link", data.navTopLink);
        } else {
          sessionStorage.setItem("nav_top_link", "");
        }

        if (
          data.services &&
          Array.isArray(data.services) &&
          data.services.length > 0
        ) {
          const services = data.services.map((service) => ({
            title: service.title,
            link: service.link,
          }));

          var ul = document.getElementById("custom-service-ul");
          var container = document.getElementById("custom-service-footer");
          document.getElementById(
            "custom-service"
          ).href = `${data.url}/services`;
          document.getElementById(
            "ft-services"
          ).href = `${data.url}/services`;

          if (services.length > 0) {
            services.forEach(function (service) {
              var li = document.createElement("li");
              li.className = "menu-item";
              var div = document.createElement("div");
              div.className = "brxe-xvkivb brxe-div";
              var a = document.createElement("a");
              a.className = "brxe-yttxdw brxe-text-link nav-nested-link";
              a.href = service.link;
              a.textContent = service.title;
              div.appendChild(a);
              li.appendChild(div);
              ul.appendChild(li);

              //footer generation
              var div = document.createElement("div");
              div.className = "brxe-sawnmd brxe-div";
              var a = document.createElement("a");
              a.className = "brxe-znrqhw brxe-text-basic footer-inner-link";
              a.href = service.link;
              a.textContent = service.title;
              div.appendChild(a);
              container.appendChild(div);
            });
          } else {
            var li = document.createElement("li");
            li.className = "menu-item";
            li.textContent = "No services found.";
            ul.appendChild(li);

            //footer
            var div = document.createElement("div");
            div.className = "brxe-sawnmd brxe-div";
            div.textContent = "No services found.";
            container.appendChild(div);
          }

          sessionStorage.setItem(
            "location_service",
            JSON.stringify(services)
          );
        } else {
          sessionStorage.setItem("location_service", JSON.stringify([]));
        }

        if (
          data.blogPage &&
          Array.isArray(data.blogPage) &&
          data.blogPage.length > 0
        ) {
          const blogLinks = data.blogPage.map((bloLink) => ({
            link: bloLink.link,
          }));
          setHrefForAll("blog-link-nav", `${data.url}/blog`);
          setHrefForAll("blog-link-footer", `${data.url}/blog`);
          sessionStorage.setItem("blog_link", JSON.stringify(blogLinks));
        } else {
          blogNavLink.style.display = "none";
          blogNavLink.style.visibility = "hidden";
          blogFooterLink.style.display = "none";
          blogFooterLink.style.visibility = "hidden";
          sessionStorage.setItem("blog_link", JSON.stringify([]));
        }

        if (
          data.wkPage &&
          Array.isArray(data.wkPage) &&
          data.wkPage.length > 0
        ) {
          const wkLinks = data.blogPage.map((wkLink) => ({
            link: wkLink.link,
          }));
          sessionStorage.setItem("wk_link", JSON.stringify(wkLinks));
        } else {
          sessionStorage.setItem("wk_link", JSON.stringify([]));
        }

        if (
          data.wrPage &&
          Array.isArray(data.wrPage) &&
          data.wrPage.length > 0
        ) {
          const wrLinks = data.wrPage.map((wrLink) => ({
            link: wrLink.link,
          }));
          sessionStorage.setItem("wr_link", JSON.stringify(wrLinks));
        } else {
          sessionStorage.setItem("wr_link", JSON.stringify([]));
        }

        if (
          data.hoPage &&
          Array.isArray(data.hoPage) &&
          data.hoPage.length > 0
        ) {
          const hoLinks = data.wrPage.map((hoLink) => ({
            link: hoLink.link,
          }));
          sessionStorage.setItem("ho_link", JSON.stringify(hoLinks));
        } else {
          sessionStorage.setItem("ho_link", JSON.stringify([]));
        }

        if (
          data.rlPage &&
          Array.isArray(data.rlPage) &&
          data.rlPage.length > 0
        ) {

          const rlPageLinks = data.rlPage.map((rlPageLink) => ({
            link: rlPageLink.link,
            terms: rlPageLink.terms,
            title: rlPageLink.title,
          }));
          // Check if any object in rlPageLinks has terms equal to 'areas-served'
          const hasAreasServed = rlPageLinks.some((rlPageLink) =>
            rlPageLink.terms.includes("Areas served")
          );

          if (hasAreasServed) {
            if (areasServedNavLink) {
              areasServedNavLink.style.display = "flex";
              areasServedNavLink.style.visibility = "visible";
              areasServedNavLink.href = `${data.url}/areas-served`;
            }
          }

          console.log(rlPageLinks)

          //areasServedNavLink.href = `${data.url}/areas-served`;
          sessionStorage.setItem("rl_arr", JSON.stringify(rlPageLinks));
          const resourceContainer =
            document.getElementById("resource-container");

          if (rlPageLinks.length > 0) {
            resourceContainer.style.display = "flex";
            resourceContainer.innerHTML = "";

            rlPageLinks.forEach((resource) => {
              const linkElement = document.createElement("span");
              linkElement.className = "brxe-text-link nav-nested-link";

              // Check for the term name and adjust link text and URL accordingly
              if (resource.terms.includes("Meet the team")) {
                linkElement.textContent = `Meet Koala Insulation of ${data.name}`;
                linkElement.onclick = () => {
                  window.location.href = `${data.url}/meet-the-team`;
                };
              } else if (resource.terms.includes("Recent Projects")) {
                linkElement.textContent = `Recent Projects of ${data.name}`;
                linkElement.onclick = () => {
                  window.location.href = `${data.url}/recent-projects`;
                };
              } else if (resource.terms.includes("Careers")) {
                linkElement.textContent = `Careers`;
                linkElement.onclick = () => {
                  window.open(`${data.url}/careers`, "_blank");
                };
              } else if (resource.terms.includes("Areas served")) {
                // Do nothing for "Areas served"
                return;
              } else {
                // Handle other term types or add a default if needed
                linkElement.textContent = resource.title;
                linkElement.onclick = () => {
                  window.location.href = resource.link;
                };
              }

              resourceContainer.appendChild(linkElement);
            });
          } else {
            resourceContainer.style.display = "none";
          }
        } else {
          sessionStorage.setItem("rl_arr", JSON.stringify([]));
        }
        

        if (data.faqs && Array.isArray(data.faqs) && data.faqs.length > 0) {
          const faqs = data.faqs.map((faq) => ({
            title: faq.title,
            content: faq.content,
          }));

          sessionStorage.setItem("related_faqs", JSON.stringify(faqs));
        } else {
          sessionStorage.setItem("related_faqs", JSON.stringify([]));
        }

        if (data.name && data.state && !document.body.classList.contains('single-location')) {
          const thank_you_header_address_element = document.getElementById("brxe-galfbb");
          if (thank_you_header_address_element) {
            // Loop through child nodes and replace only text nodes
            thank_you_header_address_element.childNodes.forEach((node) => {
              if (node.nodeType === Node.TEXT_NODE) {
                node.textContent = data.name + ", " + data.state;
              }
            });
          }
        }
        if (data.phone1 && !document.body.classList.contains('single-location') ) {

          const phoneEl = document.getElementById("brxe-mqulbj");
          if (phoneEl) {
            // Update href
            phoneEl.href = `tel:${data.phone1}`;

            // Check if span already exists after the SVG
            let span = phoneEl.querySelector("span.phone-text");
            if (!span) {
              // Create and append span
              span = document.createElement("span");
              //span.className = "phone-text";
              //span.style.marginLeft = "8px"; 
              phoneEl.appendChild(span);
            }

            // Update text
            span.textContent = data.phone1;
          }
        }



        console.log("Location Data:", data);
      })
      .then(() => {
        usaNav.style.display = "block";
        usaNav.style.visibility = "visible";
        usFooter.style.display = "flex";
        usFooter.style.visibility = "visible";

        if (elementHide) {
          elementHide.style.display = "none";
          elementHide.style.visibility = "hidden";
          elementHide.style.opacity = "0";
        }

        if (elementDropDownHide) {
          elementDropDownHide.style.display = "none";
          elementDropDownHide.style.visibility = "hidden";
        }

        if (elementFooterHide) {
          elementFooterHide.style.display = "none";
          elementFooterHide.style.visibility = "hidden";
        }

        if (locationNavLink) {
          locationNavLink.style.display = "none";
          locationNavLink.style.visibility = "hidden";
        }

        if (locationFooterLink) {
          locationFooterLink.style.display = "none";
          locationFooterLink.style.visibility = "hidden";
        }

        if (usSlider) {
          usSlider.style.display = "flex";
          usSlider.style.visibility = "visible";
        }

        if (usOurServices) {
          usOurServices.style.display = "flex";
          usOurServices.style.visibility = "visible";
        }

        if (usOurServicesSubnav) {
          usOurServicesSubnav.style.display = "flex";
          usOurServicesSubnav.style.visibility = "visible";
        }
        if (elementDropDownShow) {
          elementDropDownShow.style.display = "block";
          elementDropDownShow.style.visibility = "visible";
        }
        if (elementFooterShow) {
          elementFooterShow.style.display = "flex";
          elementFooterShow.style.visibility = "visible";
        }

        if (nationalNavQuote) {
          nationalNavQuote.style.display = "flex";
          nationalNavQuote.style.visibility = "visible";
        }

        if (localNavQuote) {
          localNavQuote.style.display = "none";
          localNavQuote.style.visibility = "hidden";
        }

        if (nationalEstimateBtn) {
          nationalEstimateBtn.style.display = "flex";
          nationalEstimateBtn.style.visibility = "visible";
        }

        if (nationalEstimateFooterBtn) {
          nationalEstimateFooterBtn.style.display = "flex";
          nationalEstimateFooterBtn.style.visibility = "visible";
        }

        if (ctaQuote) {
          ctaQuote.style.display = "flex";
          ctaQuote.style.visibility = "visible";
        }

        if (elementShow) {
          elementShow.style.display = "flex";
          elementShow.style.visibility = "visible";
        }
        if (elementLocalWidget) {
          elementLocalWidget.style.display = "block";
          elementLocalWidget.style.visibility = "visible";
        }
      })
      .catch((error) => {
        if (!document.querySelector('script[src*="cdn.nicejob.co"]')) {
        const script = document.createElement("script");
        script.src =
          "https://cdn.nicejob.co/js/sdk.min.js?id=5343641076236288";
        script.async = true; // Ensure the script loads asynchronously
        loadNiceJobAfterInteraction(script);

        script.onload = function () {
          console.log("NiceJob SDK loaded successfully.");
          const schemaObserver = new MutationObserver((mutations, observer) => {
  let removed = false;

  const ldSchemas = document.querySelectorAll('script[type="application/ld+json"]');
  ldSchemas.forEach((scriptTag) => {
    try {
      const json = JSON.parse(scriptTag.innerText);
      if (json['@type'] === 'Review') {
        if (scriptTag.parentNode) {
          scriptTag.parentNode.removeChild(scriptTag); // more reliable than .remove()
          console.log('Removed NiceJob review schema.');
          removed = true;
        }
      }
    } catch (e) {
      // Ignore parse errors
    }
  });

  if (removed) {
    observer.disconnect(); // Stop after first actual removal
  }
});

schemaObserver.observe(document.documentElement, {
  childList: true,
  subtree: true
});
          // No need to manually call NiceJob.showRecommendation(), let SDK handle it
        };

        script.onerror = function () {
          console.error("Failed to load NiceJob SDK.");
        };
        } // end duplicate guard

        console.error("Error fetching location data:", error);
        sessionStorage.setItem("location_info", "false");
        usaNav.style.display = "block";
        usaNav.style.visibility = "visible";
        usFooter.style.display = "flex";
        usFooter.style.visibility = "visible";

        if (usSlider) {
          usSlider.style.display = "flex";
          usSlider.style.visibility = "visible";
        }

        if (usOurServices) {
          usOurServices.style.display = "flex";
          usOurServices.style.visibility = "visible";
        }

        if (usOurServicesSubnav) {
          usOurServicesSubnav.style.display = "flex";
          usOurServicesSubnav.style.visibility = "visible";
        }

        if (locationNavLink) {
          locationNavLink.style.display = "block";
          locationNavLink.style.visibility = "visible";
        }

        if (localNavQuote) {
          localNavQuote.style.display = "flex";
          localNavQuote.style.visibility = "visible";
        }

        if (localEstimateBtn) {
          localEstimateBtn.style.display = "flex";
          localEstimateBtn.style.visibility = "visible";
        }

        if (localEstimateBtn1) {
          localEstimateBtn1.style.display = "flex";
          localEstimateBtn1.style.visibility = "visible";
        }

        if (locationFooterLink) {
          locationFooterLink.style.display = "block";
          locationFooterLink.style.visibility = "visible";
        }

        if (cta) {
          cta.style.display = "block";
          cta.style.visibility = "visible";
        }

        if (ctaAlt) {
          ctaAlt.style.display = "block";
          ctaAlt.style.visibility = "visible";
        }

        if (ctaAlt2) {
          ctaAlt2.style.display = "flex";
          ctaAlt2.style.visibility = "visible";
        }

        if (elementHide) {
          elementHide.style.display = "flex";
          elementHide.style.visibility = "visible";
          elementHide.style.opacity = "1";
        }

        if (elementDropDownHide) {
          elementDropDownHide.style.display = "flex";
          elementDropDownHide.style.visibility = "visible";
        }

        if (elementFooterHide) {
          elementFooterHide.style.display = "flex";
          elementFooterHide.style.visibility = "visible";
        }

        if (elementMainWidget) {
          elementMainWidget.style.display = "flex";
          elementMainWidget.style.visibility = "visible";
        }

        if (elementMainStoriesWidget) {
          elementMainStoriesWidget.style.display = "block";
          elementMainStoriesWidget.style.visibility = "visible";
        }

        sessionStorage.removeItem("location_name");
        sessionStorage.removeItem("location_address");
        sessionStorage.removeItem("location_phone_number");
        sessionStorage.removeItem("location_phone_number2");
        sessionStorage.removeItem("location_service");
        sessionStorage.removeItem("location_nicejob_id");
        sessionStorage.removeItem("location_url");
        sessionStorage.removeItem("location_key");
        sessionStorage.removeItem("location_sm_key");
        sessionStorage.removeItem("location_region");
        sessionStorage.removeItem("google_review_shortcode");
        sessionStorage.removeItem("wk_link");
        sessionStorage.removeItem("wr_link");
        sessionStorage.removeItem("ho_link");
        sessionStorage.removeItem("rl_arr");
        sessionStorage.removeItem("fb_link");
        sessionStorage.removeItem("insta_link");
        sessionStorage.removeItem("linkedin_link");
        sessionStorage.removeItem("yt_link");
        sessionStorage.removeItem("blog_link");
        sessionStorage.removeItem("location_before_head_tag");
        sessionStorage.removeItem("location_before_body_tag");
        sessionStorage.removeItem("location_after_body_tag");
        sessionStorage.removeItem("nav_top_text");
        sessionStorage.removeItem("related_faqs");
        sessionStorage.removeItem("footer_resource1_arr");
        sessionStorage.removeItem("footer_resource2_arr");
        sessionStorage.removeItem("footer_resource3_arr");
        sessionStorage.removeItem("footer_resource4_arr");
      });
  } else {
    sessionStorage.setItem("location_info", "false");
    usaNav.style.display = "block";
    usaNav.style.visibility = "visible";
    usFooter.style.display = "flex";
    usFooter.style.visibility = "visible";

    if (!document.querySelector('script[src*="cdn.nicejob.co"]')) {
    const script = document.createElement("script");
    script.src = "https://cdn.nicejob.co/js/sdk.min.js?id=5343641076236288";
    script.async = true; // Ensure the script loads asynchronously
    loadNiceJobAfterInteraction(script);

    script.onload = function () {
      console.log("NiceJob SDK loaded successfully.");
      const schemaObserver = new MutationObserver((mutations, observer) => {
  let removed = false;

  const ldSchemas = document.querySelectorAll('script[type="application/ld+json"]');
  ldSchemas.forEach((scriptTag) => {
    try {
      const json = JSON.parse(scriptTag.innerText);
      if (json['@type'] === 'Review') {
        if (scriptTag.parentNode) {
          scriptTag.parentNode.removeChild(scriptTag); // more reliable than .remove()
          console.log('Removed NiceJob review schema.');
          removed = true;
        }
      }
    } catch (e) {
      // Ignore parse errors
    }
  });

  if (removed) {
    observer.disconnect(); // Stop after first actual removal
  }
});

schemaObserver.observe(document.documentElement, {
  childList: true,
  subtree: true
});
      // No need to manually call NiceJob.showRecommendation(), let SDK handle it
    };

    script.onerror = function () {
      console.error("Failed to load NiceJob SDK.");
    };
    } // end duplicate guard

    if (usSlider) {
      usSlider.style.display = "flex";
      usSlider.style.visibility = "visible";
    }

    if (usOurServices) {
      usOurServices.style.display = "flex";
      usOurServices.style.visibility = "visible";
    }

    if (usOurServicesSubnav) {
      usOurServicesSubnav.style.display = "flex";
      usOurServicesSubnav.style.visibility = "visible";
    }

    if (locationNavLink) {
      locationNavLink.style.display = "block";
      locationNavLink.style.visibility = "visible";
    }

    if (localNavQuote) {
      localNavQuote.style.display = "flex";
      localNavQuote.style.visibility = "visible";
    }

    if (localEstimateBtn) {
      localEstimateBtn.style.display = "flex";
      localEstimateBtn.style.visibility = "visible";
    }

    if (localEstimateBtn1) {
      localEstimateBtn1.style.display = "flex";
      localEstimateBtn1.style.visibility = "visible";
    }

    if (locationFooterLink) {
      locationFooterLink.style.display = "block";
      locationFooterLink.style.visibility = "visible";
    }

    if (cta) {
      cta.style.display = "block";
      cta.style.visibility = "visible";
    }

    if (ctaAlt) {
      ctaAlt.style.display = "block";
      ctaAlt.style.visibility = "visible";
    }

    if (ctaAlt2) {
      ctaAlt2.style.display = "flex";
      ctaAlt2.style.visibility = "visible";
    }

    if (elementHide) {
      elementHide.style.display = "flex";
      elementHide.style.visibility = "visible";
      elementHide.style.opacity = "1";
    }

    if (elementDropDownHide) {
      elementDropDownHide.style.display = "flex";
      elementDropDownHide.style.visibility = "visible";
    }

    if (elementFooterHide) {
      elementFooterHide.style.display = "flex";
      elementFooterHide.style.visibility = "visible";
    }

    if (elementMainWidget) {
      elementMainWidget.style.display = "flex";
      elementMainWidget.style.visibility = "visible";
    }

    if (elementMainStoriesWidget) {
      elementMainStoriesWidget.style.display = "block";
      elementMainStoriesWidget.style.visibility = "visible";
    }

    sessionStorage.removeItem("location_name");
    sessionStorage.removeItem("location_address");
    sessionStorage.removeItem("location_phone_number");
    sessionStorage.removeItem("location_phone_number2");
    sessionStorage.removeItem("location_service");
    sessionStorage.removeItem("location_nicejob_id");
    sessionStorage.removeItem("location_url");
    sessionStorage.removeItem("location_key");
    sessionStorage.removeItem("location_sm_key");
    sessionStorage.removeItem("location_region");
    sessionStorage.removeItem("google_review_shortcode");
    sessionStorage.removeItem("wk_link");
    sessionStorage.removeItem("wr_link");
    sessionStorage.removeItem("ho_link");
    sessionStorage.removeItem("rl_arr");
    sessionStorage.removeItem("fb_link");
    sessionStorage.removeItem("insta_link");
    sessionStorage.removeItem("linkedin_link");
    sessionStorage.removeItem("yt_link");
    sessionStorage.removeItem("blog_link");
    sessionStorage.removeItem("location_before_head_tag");
    sessionStorage.removeItem("location_before_body_tag");
    sessionStorage.removeItem("location_after_body_tag");
    sessionStorage.removeItem("nav_top_text");
    sessionStorage.removeItem("related_faqs");
    sessionStorage.removeItem("footer_resource1_arr");
    sessionStorage.removeItem("footer_resource2_arr");
    sessionStorage.removeItem("footer_resource3_arr");
    sessionStorage.removeItem("footer_resource4_arr");
  }
});

// swiper slider photos
function numberWithZero(num) {
  if (num < 10) {
    return "0" + num;
  } else {
    return num;
  }
}

$(".slider-photo-wrapper").each(function (index) {
  let totalSlides = numberWithZero(
    $(this).find(".swiper-slide.is-slider-main-slide").length
  );
  $(".swiper-number-total").text(totalSlides);
  let loopMode = false;
  if ($(this).attr("loop-mode") === "true") {
    loopMode = true;
  }
  let sliderDuration = 300;
  if ($(this).attr("slider-duration") !== undefined) {
    sliderDuration = +$(this).attr("slider-duration");
  }
  const swiper = new Swiper($(this).find(".swiper")[0], {
    speed: 1000,
    loop: true,
    autoHeight: false,
    centeredSlides: loopMode,
    followFinger: true,
    freeMode: false,
    slideToClickedSlide: false,
    parallax: true,
    slidesPerView: 1,
    spaceBetween: "8%",
    rewind: false,
    mousewheel: {
      forceToAxis: true,
    },
    keyboard: {
      enabled: true,
      onlyInViewport: true,
    },
    breakpoints: {
      // mobile landscape
      480: {
        slidesPerView: 1,
        spaceBetween: 24,
      },
      // tablet
      768: {
        slidesPerView: 2,
        spaceBetween: 24,
      },
      // desktop
      992: {
        slidesPerView: 2,
        spaceBetween: 24,
      },
    },
    pagination: {
      el: $(this).find(".swiper-bullet-wrapper")[0],
      bulletActiveClass: "is-active",
      bulletClass: "swiper-bullet",
      bulletElement: "button",
      clickable: true,
    },
    navigation: {
      nextEl: $(this).find(".swiper-next")[0],
      prevEl: $(this).find(".swiper-prev")[0],
      disabledClass: "is-disabled",
    },
    scrollbar: {
      el: $(this).find(".swiper-drag-wrapper")[0],
      draggable: true,
      dragClass: "swiper-drag",
      snapOnRelease: true,
    },
    slideActiveClass: "is-active",
    slideDuplicateActiveClass: "is-active",
  });

  swiper.on("slideChange", function (e) {
    //console.log(swiper.realIndex);
    let slidenumber = numberWithZero(e.realIndex + 1);
    $(".swiper-number-current").text(slidenumber);
  });
});

// swiper slider why reinsulate before after photos

$(".slider-before-after-wrapper").each(function (index) {
  let totalSlides = numberWithZero(
    $(this).find(".swiper-slide.is-slider-main-slide").length
  );
  $(".swiper-number-total").text(totalSlides);
  let loopMode = false;
  if ($(this).attr("loop-mode") === "true") {
    loopMode = true;
  }
  let sliderDuration = 300;
  if ($(this).attr("slider-duration") !== undefined) {
    sliderDuration = +$(this).attr("slider-duration");
  }
  const swiper = new Swiper($(this).find(".swiper")[0], {
    speed: 1000,
    loop: true,
    autoHeight: false,
    centeredSlides: loopMode,
    followFinger: true,
    freeMode: false,
    slideToClickedSlide: false,
    parallax: true,
    slidesPerView: 1,
    spaceBetween: "8%",
    rewind: false,
    mousewheel: {
      forceToAxis: true,
    },
    keyboard: {
      enabled: true,
      onlyInViewport: true,
    },
    breakpoints: {
      // mobile landscape
      480: {
        slidesPerView: 1,
        spaceBetween: 80,
      },
      // tablet
      768: {
        slidesPerView: 1,
        spaceBetween: 80,
      },
      // desktop
      992: {
        slidesPerView: 1,
        spaceBetween: 80,
      },
    },
    pagination: {
      el: $(this).find(".swiper-bullet-wrapper")[0],
      bulletActiveClass: "is-active",
      bulletClass: "swiper-bullet",
      bulletElement: "button",
      clickable: true,
    },
    navigation: {
      nextEl: $(this).find(".swiper-next")[0],
      prevEl: $(this).find(".swiper-prev")[0],
      disabledClass: "is-disabled",
    },
    scrollbar: {
      el: $(this).find(".swiper-drag-wrapper")[0],
      draggable: true,
      dragClass: "swiper-drag",
      snapOnRelease: true,
    },
    slideActiveClass: "is-active",
    slideDuplicateActiveClass: "is-active",
  });

  swiper.on("slideChange", function (e) {
    //console.log(swiper.realIndex);
    let slidenumber = numberWithZero(e.realIndex + 1);
    $(".swiper-number-current").text(slidenumber);
  });
});

// swiper slider services

$(".services-slider-main-component").each(function (index) {
  let totalSlides = numberWithZero(
    $(this).find(".service-card-home.swiper-slide").length
  );
  $(".swiper-number-total").text(totalSlides);
  let loopMode = false;
  if ($(this).attr("loop-mode") === "true") {
    loopMode = true;
  }
  let sliderDuration = 300;
  if ($(this).attr("slider-duration") !== undefined) {
    sliderDuration = +$(this).attr("slider-duration");
  }
  const swiper = new Swiper($(this).find(".swiper")[0], {
    speed: 1000,
    loop: true,
    autoHeight: false,
    centeredSlides: false,
    followFinger: true,
    freeMode: false,
    slideToClickedSlide: false,
    parallax: true,
    slidesPerView: 1,
    spaceBetween: "8%",
    rewind: false,
    mousewheel: {
      forceToAxis: true,
    },
    keyboard: {
      enabled: true,
      onlyInViewport: true,
    },
    breakpoints: {
      // mobile landscape
      480: {
        slidesPerView: 1,
        spaceBetween: 24,
        slidesPerGroup: 1,
      },
      // tablet
      768: {
        slidesPerView: 2,
        spaceBetween: 24,
        slidesPerGroup: 2,
      },
      // desktop
      992: {
        slidesPerView: 3,
        slidesPerGroup: 3,
        spaceBetween: 24,
      },
    },
    pagination: {
      el: $(this).find(".swiper-bullet-wrapper")[0],
      bulletActiveClass: "is-active",
      bulletClass: "swiper-bullet",
      bulletElement: "button",
      clickable: true,
    },
    navigation: {
      nextEl: $(this).find(".swiper-next")[0],
      prevEl: $(this).find(".swiper-prev")[0],
      disabledClass: "is-disabled",
    },
    scrollbar: {
      el: $(this).find(".swiper-drag-wrapper")[0],
      draggable: true,
      dragClass: "swiper-drag",
      snapOnRelease: true,
    },
    slideActiveClass: "is-active",
    slideDuplicateActiveClass: "is-active",
  });

  swiper.on("slideChange", function (e) {
    //console.log(swiper.realIndex);
    let slidenumber = numberWithZero(e.realIndex + 1);
    $(".swiper-number-current").text(slidenumber);
  });
});

$(document).ready(function () {
  $(".acc-head1").on("click", function () {
    if ($(this).hasClass("active")) {
      $(this).siblings(".acc-content1").slideUp();
      $(this).removeClass("active");
    } else {
      $(".acc-content1").slideUp();
      $(".acc-head1").removeClass("active");
      $(this).siblings(".acc-content1").slideToggle();
      $(this).toggleClass("active");
    }
  });
});

$(document).ready(function () {
  $(".tab-menu-list .steps-tab-title:nth-child(1) .step-tab-head").addClass(
    "active"
  );
  $(
    ".tab-menu-list .steps-tab-title:nth-child(1) .step-tab-content"
  ).slideDown();
  $(".step-tab-head").on("click", function () {
    if ($(this).hasClass("active")) { } else {
      $(".step-tab-content").slideUp();
      $(".step-tab-head").removeClass("active");
      $(this).siblings(".step-tab-content").slideToggle();
      $(this).toggleClass("active");
    }
  });
});

$(document).ready(function () {
  // Initially hide all accordion content except the first
  $(".acc-container3 .acc-content3").hide();
  $(".acc-container3 .acc3:first-child .acc-content3").show();
  $(".acc-container3 .acc3:first-child .accordian-icon-wrapper img").css(
    "transform",
    "rotate(180deg)"
  );
  $(".acc-container3 .acc3:first-child .acc-head3").addClass("active");

  // Add click event for accordion headers
  $(".acc-head3").on("click", function () {
    const $accordionHead = $(this);
    const $accordionContent = $accordionHead.next(".acc-content3");
    const $icon = $accordionHead.find(".accordian-icon-wrapper img");

    if ($accordionHead.hasClass("active")) {
      // Collapse the currently active accordion
      $accordionContent.slideUp();
      $icon.css("transform", "rotate(0deg)");
      $accordionHead.removeClass("active");
    } else {
      // Collapse all accordions
      $(".acc-content3").slideUp();
      $(".accordian-icon-wrapper img").css("transform", "rotate(0deg)");
      $(".acc-head3").removeClass("active");

      // Expand the clicked accordion
      $accordionContent.slideDown();
      $icon.css("transform", "rotate(180deg)");
      $accordionHead.addClass("active");
    }
  });
});

$(document).ready(function () {
  // Initially hide all accordion content except the first
  $(".acc-container4 .acc-content4").hide();
  $(".acc-container4 .acc4:first-child .acc-content4").show();
  $(".acc-container4 .acc4:first-child .acc-head4").addClass("active");
  $(".acc-container4 .acc4:first-child .accordian-icon-wrapper .acc-icon-min").css("display", "block");
  $(".acc-container4 .acc4:first-child .accordian-icon-wrapper .acc-icon-plus").css("display", "none");

  // Add click event for accordion headers
  $(".acc-head4").on("click", function () {
    // Only apply the fix on the national FAQ page
    if (window.location.pathname === "/faq") {
      e.preventDefault();    // stop <a href="#"> navigation
      e.stopPropagation();   // avoid any global click-to-close firing
    }
    const $accordionHead = $(this);
    const $accordionContent = $accordionHead.next(".acc-content4");
    const $iconPlus = $accordionHead.find(".acc-icon-plus");
    const $iconMin = $accordionHead.find(".acc-icon-min");

    if ($accordionHead.hasClass("active")) {
      // Collapse the currently active accordion
      $accordionContent.slideUp();
      $iconPlus.css("display", "block");
      $iconMin.css("display", "none");
      $accordionHead.removeClass("active");
    } else {
      // Collapse all accordions
      $(".acc-content4").slideUp();
      $(".acc-head4").removeClass("active");
      $(".acc-head4 .acc-icon-plus").css("display", "block");
      $(".acc-head4 .acc-icon-min").css("display", "none");

      // Expand the clicked accordion
      $accordionContent.slideDown();
      $iconPlus.css("display", "none");
      $iconMin.css("display", "block");
      $accordionHead.addClass("active");
    }
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
      var mapElement = document.getElementById("map1");
      if (mapElement) {
        var map = new google.maps.Map(document.getElementById("map1"), {
          zoom: 4,
          center: new google.maps.LatLng(37.09024, -95.712891),
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          styles: [{
            elementType: "geometry",
            stylers: [{
              color: "#9ec7ba"
            }]
          },
          {
            elementType: "labels.icon",
            stylers: [{
              visibility: "off"
            }]
          },
          {
            elementType: "labels.text.fill",
            stylers: [{
              color: "#fcfcff"
            }]
          },
          {
            elementType: "labels.text.stroke",
            stylers: [{
              visibility: "off"
            }]
          },
          {
            featureType: "administrative",
            elementType: "geometry",
            stylers: [{
              color: "#9ec7ba"
            }],
          },
          {
            featureType: "administrative.country",
            elementType: "labels.text.fill",
            stylers: [{
              visibility: "off"
            }],
          },
          {
            featureType: "administrative.land_parcel",
            stylers: [{
              visibility: "off"
            }],
          },
          {
            featureType: "administrative.locality",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#fcfcff"
            }],
          },
          {
            featureType: "poi",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#fcfcff"
            }],
          },
          {
            featureType: "poi.park",
            elementType: "geometry",
            stylers: [{
              color: "#99a9bb"
            }],
          },
          {
            featureType: "poi.park",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#fcfcff"
            }],
          },
          {
            featureType: "road",
            elementType: "geometry.fill",
            stylers: [{
              color: "#7EB4A3"
            }],
          },
          {
            featureType: "road.arterial",
            elementType: "geometry",
            stylers: [{
              color: "#7EB4A3"
            }],
          },
          {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
              color: "#7EB4A3"
            }],
          },
          {
            featureType: "road.highway.controlled_access",
            elementType: "geometry",
            stylers: [{
              color: "#7EB4A3"
            }],
          },
          {
            featureType: "transit",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#fcfcff"
            }],
          },
          {
            featureType: "water",
            elementType: "geometry",
            stylers: [{
              color: "#7EB4A3"
            }],
          },
          ],
        });

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
            return zip.trim(); // Trim each zip code for consistency
          });

          // Clear existing markers
          gmarkers.forEach(function (marker) {
            marker.setMap(null);
          });
          gmarkers = [];

          var foundLocations = locations.filter(function (location) {
            var zipCodes = [location[5]]; // Add primary zip code

            // Check if any zip in zips matches any zip in zipCodes
            return zips.some(function (zip) {
              return zipCodes.includes(zip);
            });
          });

          if (foundLocations.length > 0) {
            foundLocations.forEach(function (location) {
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

            if (closestZip !== null) {
              var foundLocations = locations.filter(function (location) {
                var zipCodes = [location[5]]; // Add primary zip code
                const additionalZipCodes = location[7] ?
                  location[7].split(",").map((zip) => zip.trim()) :
                  [];

                var allZips = [...zipCodes, ...additionalZipCodes];

                // Check if any zip in zips matches any zip in zipCodes
                return allZips.includes(closestZip);
              });

              // Automatically display the infowindow for the first matched location
              var firstLocation = foundLocations[0];
              var firstMarker = gmarkers[0];
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

              // Zoom and center the map on the first matched location
              map.setCenter(
                new google.maps.LatLng(firstLocation[1], firstLocation[2])
              );
              map.setZoom(4);

              handleScrollAndHighlightPrimaryZips(zips, closestZip);
            } else {
              // Automatically display the infowindow for the first matched location
              var firstLocation = foundLocations[0];
              var firstMarker = gmarkers[0];
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

              // Zoom and center the map on the first matched location
              map.setCenter(
                new google.maps.LatLng(firstLocation[1], firstLocation[2])
              );
              map.setZoom(4);

              handleScrollAndHighlightPrimaryZips(zips, closestZip);
            }
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
            const zipCodes = location[7] ?
              location[7].split(",").map((zip) => zip.trim()) :
              [];
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
            const additionalZips = element.querySelector(".additional-zipcodes") ?
              element
                .querySelector(".additional-zipcodes")
                .innerText.split(",")
                .map((zip) => zip.trim()) :
              [];
            return zips.some((zip) => additionalZips.includes(zip));
          });

          if (infoContentArray.length > 0) {
            // Scroll to the first matched element
            const firstElement = infoContentArray[0];
            document.querySelector("#brxe-htrqiy").scrollTo({
              top: firstElement.offsetTop -
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
              const additionalZips = element.querySelector(".additional-zipcodes") ?
                element
                  .querySelector(".additional-zipcodes")
                  .innerText.split(",")
                  .map((zip) => zip.trim()) :
                [];

              allZips = [...primaryZip, ...additionalZips];
              return allZips.includes(closestZip);
            });

            if (infoContentArray.length > 0) {
              // Scroll to the first matched element
              const firstElement = infoContentArray[0];
              document.querySelector("#brxe-htrqiy").scrollTo({
                top: firstElement.offsetTop -
                  document.querySelector("#brxe-htrqiy").offsetTop,
                behavior: "smooth",
              });
            }
          } else {
            // Scroll to the first matched element
            const firstElement = infoContentArray[0];
            document.querySelector("#brxe-htrqiy").scrollTo({
              top: firstElement.offsetTop -
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
                        console.log("Nearby ZIP Codes: hi", nearbyZips);

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
                            koalaData.ajax_url, {
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
                              koalaData.ajax_url, {
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
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const servicesList = document.getElementById("usa-our-services");
  const servicesListCa = document.getElementById("ca-our-services");

  if (servicesList) {
    const servicesItems = servicesList.querySelectorAll(".service-item");

    servicesItems.forEach((item, index) => {
      if (index % 2 !== 0) {
        item.style.flexDirection = "row-reverse";
      }
    });
  }

  if (servicesListCa) {
    const servicesItems = servicesListCa.querySelectorAll(".service-item");

    servicesItems.forEach((item, index) => {
      if (index % 2 !== 0) {
        item.style.flexDirection = "row-reverse";
      }
    });
  }

  /*const serviceLinks = document.querySelectorAll(".service-link-tag");

  serviceLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      const targetId = this.id;
      const targetItem = document.querySelector(
        `.service-item[id="${targetId}"]`
      );

      if (targetItem) {
        const stickyNavHeight =
          document.getElementById("brx-header").offsetHeight;

        const targetPosition =
          targetItem.getBoundingClientRect().top +
          window.pageYOffset -
          stickyNavHeight;

        window.scrollTo({
          top: targetPosition,
          behavior: "smooth",
        });
      }
    });
  });*/
});

window.addEventListener("load", function () {
  var is_location_page = koalaData.is_location;
  var mapElement = document.getElementById("map1");

  if (!is_location_page && mapElement) {
    var locations = [];

    var dnyPlaces = koalaData.zip_locations;

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
    console.log(locations);
    var gmarkers = [];


    var map = new google.maps.Map(document.getElementById("map1"), {
      zoom: 4,
      center: new google.maps.LatLng(37.09024, -95.712891),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      styles: [{
        elementType: "geometry",
        stylers: [{
          color: "#9ec7ba"
        }]
      },
      {
        elementType: "labels.icon",
        stylers: [{
          visibility: "off"
        }]
      },
      {
        elementType: "labels.text.fill",
        stylers: [{
          color: "#fcfcff"
        }]
      },
      {
        elementType: "labels.text.stroke",
        stylers: [{
          visibility: "off"
        }]
      },
      {
        featureType: "administrative",
        elementType: "geometry",
        stylers: [{
          color: "#9ec7ba"
        }],
      },
      {
        featureType: "administrative.country",
        elementType: "labels.text.fill",
        stylers: [{
          visibility: "off"
        }],
      },
      {
        featureType: "administrative.land_parcel",
        stylers: [{
          visibility: "off"
        }],
      },
      {
        featureType: "administrative.locality",
        elementType: "labels.text.fill",
        stylers: [{
          color: "#fcfcff"
        }],
      },
      {
        featureType: "poi",
        elementType: "labels.text.fill",
        stylers: [{
          color: "#fcfcff"
        }],
      },
      {
        featureType: "poi.park",
        elementType: "geometry",
        stylers: [{
          color: "#99a9bb"
        }],
      },
      {
        featureType: "poi.park",
        elementType: "labels.text.fill",
        stylers: [{
          color: "#fcfcff"
        }],
      },
      {
        featureType: "road",
        elementType: "geometry.fill",
        stylers: [{
          color: "#7EB4A3"
        }],
      },
      {
        featureType: "road.arterial",
        elementType: "geometry",
        stylers: [{
          color: "#7EB4A3"
        }],
      },
      {
        featureType: "road.highway",
        elementType: "geometry",
        stylers: [{
          color: "#7EB4A3"
        }],
      },
      {
        featureType: "road.highway.controlled_access",
        elementType: "geometry",
        stylers: [{
          color: "#7EB4A3"
        }],
      },
      {
        featureType: "transit",
        elementType: "labels.text.fill",
        stylers: [{
          color: "#fcfcff"
        }],
      },
      {
        featureType: "water",
        elementType: "geometry",
        stylers: [{
          color: "#7EB4A3"
        }],
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

    $(document).ready(function () {
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
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const items = document.querySelectorAll(".location-item");
  const columns = 3;
  const rowsToShow = 3;
  const maxVisible = columns * rowsToShow;

  items.forEach((item, i) => {
    if (i >= maxVisible) item.style.display = "none";
  });

  const showMoreBtn = document.getElementById("show-more-btn");
  if (showMoreBtn) {

    showMoreBtn.addEventListener("click", function () {
      items.forEach(item => item.style.display = "grid");
      showMoreBtn.style.display = "none";
    });
  }
});

// document.addEventListener("DOMContentLoaded", function () {
//   const interval = setInterval(function () {
//     const njReviewCountEl = document.querySelector('.nj-trust__total');
//     const targetDiv = document.getElementById('review-count');

//     if (njReviewCountEl && targetDiv) {
//       targetDiv.textContent = njReviewCountEl.textContent;
//       clearInterval(interval);
//     }
//   }, 200);
// });


document.addEventListener("DOMContentLoaded", function () {
  const interval = setInterval(function () {
    // 1. Get references to all necessary elements first.
    const localWidget = document.getElementById('local-page-widget');
    const mainWidget = document.getElementById('main-page-widget');
    const targetDiv = document.getElementById('review-count');

    // If the target div doesn't exist on the page, stop trying.
    if (!targetDiv) {
      clearInterval(interval);
      return;
    }

    // 2. Determine if the local widget is currently visible.
    // We use getComputedStyle because it checks the actual rendered style,
    // not just inline styles.
    const isLocalWidgetHidden = !localWidget || window.getComputedStyle(localWidget).display === 'none';

    // 3. Define a variable to hold the element we find.
    let njReviewCountEl = null;

    // 4. Conditionally search for the review count element in the correct parent widget.
    if (isLocalWidgetHidden) {
      // If the local widget is hidden, search inside the main (national) widget.
      if (mainWidget) {
        njReviewCountEl = mainWidget.querySelector('.nj-trust__total');
      }
    } else {
      // Otherwise, the local widget is visible, so search inside it.
      njReviewCountEl = localWidget.querySelector('.nj-trust__total');
    }

    // 5. If we found the review count element AND the target div exists, update and stop.
    if (njReviewCountEl && targetDiv) {
      targetDiv.textContent = njReviewCountEl.textContent;
      clearInterval(interval); // Stop the interval once the job is done.
    }
  }, 200); // Check every 200 milliseconds.
});


document.addEventListener("DOMContentLoaded", function () {
  const swiperSelector = '.swiper-partner-wrapper';
  const swiperEl = document.querySelector(swiperSelector);
  let swiperInstance = null;

  function initOrDestroySwiper() {
    const slideCount = swiperEl.querySelectorAll('.swiper-slide').length;
    const isDesktop = window.innerWidth >= 1024;

    const shouldInit = !isDesktop || slideCount > 5;

    if (shouldInit && !swiperInstance) {
      swiperInstance = new Swiper(swiperSelector, {
        loop: false,
        centeredSlides: true,
        centerInsufficientSlides: true,
        spaceBetween: 20,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        pagination: false,
        breakpoints: {
          320: {
            slidesPerView: 2,
          },
          480: {
            slidesPerView: 2,
          },
          600: {
            slidesPerView: 2,
          },
          1024: {
            slidesPerView: 'auto',
          }
        }
      });
    } else if (!shouldInit && swiperInstance) {
      swiperInstance.destroy(true, true);
      swiperInstance = null;
    }
  }

  // Run once on load
  if (swiperEl) {
    initOrDestroySwiper();

    // Re-check on window resize
    window.addEventListener('resize', function () {
      initOrDestroySwiper();
    });
  }
});


(function() {
  fetch(window.location.href, { method: 'HEAD', cache: 'no-cache' })
    .then(response => {
      const cfCacheStatus = response.headers.get('cf-cache-status');
      const cfEdgeCache   = response.headers.get('cf-edge-cache');

      if (
        cfCacheStatus &&
        cfCacheStatus.toUpperCase() === 'HIT' &&
        cfEdgeCache &&
        cfEdgeCache.includes('platform=wordpress')
      ) {
        if (typeof gtag === "function") {
          gtag('event', 'cloudflare_cache_hit', {
            event_category: 'performance',
            event_label: 'APO active'
          });
        }
      }
    });
})();

document.addEventListener("DOMContentLoaded", function () {
  // Target only links inside this section
  const section = document.querySelector("#brxe-zxwvoj");
  if (!section) return;

  // List of phone link IDs to update
  const targetIds = ["brxe-froqkz", "phone1-wrapper", "loc-phone-number", "phone2-wrapper"];

  targetIds.forEach(function (id) {
    const link = section.querySelector("#" + id);
    if (!link) return;

    // Get phone number from text or href
    let phoneText = link.textContent.trim() || link.getAttribute("href");
    let digits = phoneText.replace(/\D+/g, ""); // strip everything except digits

    if (digits) {
      // Always prepend +1 before digits
      const telHref = "tel:+1" + digits;
      link.setAttribute("href", telHref);

      // Optional: also update the visible text
      // link.textContent = "+1" + digits;
    }
  });
});

document.addEventListener('DOMContentLoaded', function() {
    // Get the form ID from URL (?form=4865)
    const urlParams = new URLSearchParams(window.location.search);
    let formId = urlParams.get('form');

    if (urlParams.has('form') && !formId) {
        formId = '4865'; 
    }


    if (!formId) return;

    setTimeout(() => {
        if (typeof bricksOpenPopup === 'function') {
            bricksOpenPopup(formId);
        } else {
            console.error('Bricks Popup API not found.');
        }
    }, 500);
});

// Re-trigger CallRail swap when the quote popup becomes visible.
// swap.js skips hidden elements on initial scan, and all-pages.js sets the phone
// number async — so we watch for Bricks removing the .hide class from the popup.
(function () {
    var popup = document.querySelector('[data-popup-id="4865"]');
    if (!popup) return;

    var observer = new MutationObserver(function () {
        if (!popup.classList.contains('hide')) {
            setTimeout(function () {
                if (typeof CallTrk !== 'undefined' && typeof CallTrk.loadTagsFromDOM === 'function') {
                    CallTrk.loadTagsFromDOM();
                }
            }, 300);
        }
    });

    observer.observe(popup, { attributes: true, attributeFilter: ['class'] });
})();
