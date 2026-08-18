window.initZipShapeMaps = async function () {
    const configs = window.zipShapeMapConfigs || [];

    for (const config of configs) {
        await window.renderOneZipShapeMap(config);
    }
};

window.renderOneZipShapeMap = async function (config) {
    const el = document.getElementById(config.id);

    if (!el) {
        return;
    }

    const placeholder = el.parentElement
        ? el.parentElement.querySelector('[data-zip-shape-map-load]')
        : null;

    if (placeholder) {
        placeholder.hidden = true;
        placeholder.style.display = 'none';
    }

    const pin = {
        lat: Number(config.lat),
        lng: Number(config.lng)
    };

    const map = new google.maps.Map(el, {
        center: pin,
        zoom: Number(config.zoom) || 10,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true,
        gestureHandling: 'none',
        zoomControl: false,
        keyboardShortcuts: false,
        clickableIcons: false,
        streetViewControl: false,
        fullscreenControl: false,
        mapTypeControl: false,
        scaleControl: false,
        rotateControl: false,
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

    if (config.showPin) {
        new google.maps.Marker({
            map: map,
            position: pin,
            icon: "/wp-content/uploads/2024/09/map-pin.svg",
        });
    }

    if (config.showBoundary === false) {
        return;
    }

    if (!Array.isArray(config.zipcodes) || !config.zipcodes.length) {
        return;
    }

    try {
        const shape = await window.getCachedZipShape(config.zipcodes);

        map.data.addGeoJson(shape);

        map.data.setStyle({ //#1D6662
            fillColor: '#1D6662',
            fillOpacity: 0,
            strokeColor: '#1D6662',
            strokeWeight: 3,
            clickable: false
        });

        const bounds = new google.maps.LatLngBounds();

        map.data.forEach(function (feature) {
            feature.getGeometry().forEachLatLng(function (latLng) {
                bounds.extend(latLng);
            });
        });

        if (config.showPin) {
            bounds.extend(pin);
        }

        map.fitBounds(bounds);

    } catch (error) {
        console.error('ZIP boundary map error:', error);
    }
};

window.getCachedZipShape = async function (zipcodes) {
    const sortedZips = [...zipcodes].sort();
    const browserCacheKey = 'zipShapeMapServerV4:' + sortedZips.join(',');

    const cached = localStorage.getItem(browserCacheKey);

    if (cached) {
        try {
            return JSON.parse(cached);
        } catch (error) {
            localStorage.removeItem(browserCacheKey);
        }
    }

    const restUrl = window.zipShapeMapRestUrl + '?zips=' + encodeURIComponent(sortedZips.join(','));

    const response = await fetch(restUrl);

    const geoJson = await response.json();

    const shape = geoJson;

    try {
        localStorage.setItem(browserCacheKey, JSON.stringify(shape));
    } catch (error) {
        console.warn('Could not save ZIP shape to browser cache.', error);
    }

    return shape;
};

// Lazy-load Google Maps API only when a map container is near the viewport
(function () {
    var mapsLoaded = false;
    var mapRequested = false;

    function hasMapsConsent() {
        return typeof window.cmplz_has_consent !== 'function' || window.cmplz_has_consent('marketing');
    }

    function openConsentChoices() {
        var manageConsent = document.querySelector('.cmplz-manage-consent');

        if (manageConsent) {
            manageConsent.click();
        }
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-zip-shape-map-load]');

        if (!trigger || hasMapsConsent()) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        openConsentChoices();
    }, true);

    function requestMapsApi() {
        mapRequested = true;

        if (hasMapsConsent()) {
            loadMapsApi();
        }
    }

    function loadMapsApi() {
        if (mapsLoaded) return;
        mapsLoaded = true;

        if (window.google && window.google.maps) {
            window.initZipShapeMaps();
            return;
        }

        var s = document.createElement('script');
        s.src = 'https://maps.googleapis.com/maps/api/js?key=' + window.zipShapeMapApiKey + '&callback=initZipShapeMaps&loading=async&v=weekly';
        s.async = true;
        document.head.appendChild(s);
    }

    var maps = document.querySelectorAll('.zip-shape-map-shell');

    if (!maps.length) return;

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    observer.disconnect();
                    requestMapsApi();
                }
            });
        }, { rootMargin: '300px' });

        maps.forEach(function (el) { observer.observe(el); });
    }

    maps.forEach(function (el) {
        var trigger = el.querySelector('[data-zip-shape-map-load]');
        if (trigger) trigger.addEventListener('click', requestMapsApi, { once: true });
    });

    document.addEventListener('cmplz_enable_category', function (event) {
        if (mapRequested && event.detail && event.detail.category === 'marketing') {
            loadMapsApi();
        }
    });
})();
