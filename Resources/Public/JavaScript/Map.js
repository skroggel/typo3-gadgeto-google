/*!
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 *
 * Last updated: 01.12.2024
 * v1.0.2
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * Inspired by: https://jsfiddle.net/gh/get/library/pure/googlemaps/js-samples/tree/master/dist/samples/advanced-markers-html/jsfiddle
 *
 * Configuration-Object:
 * ========================
 * {
 *    "apiKey": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
 *    "filterButtonClass": "map-filter-button",
 *    "consentButtonClass": "map-consent-button",
 *    "mapContainerId": "tx-gadgetogoogle-map",
 *    "clusterMarkerContainerId": "tx-gadgetogoogle-cluster",
 *    "cookieName": "consent-google-map",
 *    "mapConfig": {
 *        "zoom": 12,
 *        "mapTypeControl": false,
 *        "streetViewControl": false,
 *        "options": {
 *            "gestureHandling": "greedy"
 *        },
 *        "mapId": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
 *        "center": {
 *            "lat": 50.1206911,
 *            "lng": 8.6379318
 *        },
 *        "mapTypeId": "xxxxxxxxxxxxxxxxxxxxxxxxx"
 *    },
 *    "data": [
 *        {
 *            "id": 1,
 *            "label": "Place 1",
 *            "categories": "4,2",
 *            "overlayContainerId": "tx-madj2k-map-overlay-12bf6a6119eba6800b0c5a28099f5502",
 *            "position": {
 *                "lat": 50.1206911,
 *                "lng": 8.6379318
 *            }
 *        },
 *        {
 *            "id": 2,
 *            "label": "Place 2",
 *            "categories": "1",
 *            "overlayContainerId": "tx-madj2k-map-overlay-389c49ca4b5e1010ddc708c1ee019f37",
 *            "position": {
 *                "lat": 50.082787,
 *                "lng": 8.6505285
 *            }
 *        }
 *    ],
 *    "boundaryPositions: [
 *        0: {
 *            "lat": 50.1206911,
 *            "lng": 8.6379318
 *        },
 *        1: {
 *            "lat": 50.082787,
 *            "lng": 8.6505285
*         }
 *    ]
 * }
 *
 * Filter-Buttons:
 * ========================
 * <button class="map-filter-button" data-category="1">Filter by Category 1</button>
 *
 *
 * HTML-Container of map with consent:
 * ========================
 * <div class="map-container" id="madj2k-map">
 *     <div class="map-consent">
 *        <div class="map-consent-inner">
 *             <p class="map-consent-text">
 *                 A map from Google Maps is integrated at this point.<br>Data may be transferred to Google when the map is loaded.
 *             </p>
 *             <button class="map-consent-button" title="Karte laden">
 *                  Load Map
 *             </button>
 *         </div>
 *     </div>
 * </div>
*
 *
 * Init Map:
 * ========================
 * <script type="module">
 *      import GadgetoGoogleMaps from "path/to/this/script.mjs";
 *      const Map = new GadgetoGoogleMaps(configuration);
 * </script>
 *
 */
import { MarkerClusterer } from "https://cdn.skypack.dev/@googlemaps/markerclusterer@2.3.1";

export default class GadgetoGoogleMaps {

  settings = {
    apiKey: '',
    mapContainerId: '#tx-gadgetogoogle-map',
    clusterMarkerContainerId: '#tx-gadgetogoogle-map-cluster',
    filterButtonClass: 'js-gadgetogoogle-map-filter-btn',
    consentButtonClass: 'js-gadgetogoogle-map-consent-btn',
    cookieName: 'gadgetogoogle-consent',
    mapConfig: {
      zoom: 12,
      mapTypeControl: false,
      streetViewControl: false,
    },
    data: [],
    boundaryPositions: [],
    canvas: {
      enabled: false,
      jsonCoordinates: null,
      fillStyle: 'rgba(255, 245, 245, 0.5)'
    }
  }
  markers = [];
  consent = [];
  filters = [];
  clusterRenderer = null;
  markerClusterer = null;
  map = null;
  canvas = null;

  /**
   * @param settings
   */
  constructor (settings) {
    this.settings =  {...this.settings, ...settings}
    this.consent = document.getElementsByClassName(this.settings.consentButtonClass);
    if ((this.consent.length === 0)  || (document.cookie.indexOf(this.settings.cookieName + '=1') > -1)) {
      this.initApi();
      this.initMap();
    } else {
      const consentButton = this.consent[0];
      consentButton.addEventListener('click', () => {
        this.initApi();
        this.initMap();
        document.cookie = this.settings.cookieName + '=1';
        consentButton.classList.add('active');
      });
    }
  }

  /**
   * Init Google API
   */
  initApi() {
    (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
      key: this.settings.apiKey,
      v: "weekly",
      // Use the v parameter to indicate the version to use (weekly, beta, alpha, etc.).
      // Add other bootstrap parameters as needed, using camel case.
    });
  }


  /**
   * Initialize the map
   * @returns {Promise<void>}
   */
  async initMap() {
    // Request needed libraries.
    const {Map} = await google.maps.importLibrary('maps');
    const {AdvancedMarkerElement} = await google.maps.importLibrary('marker');

    if (this.settings.mapContainerId.length) {
      this.map = new Map(document.getElementById(this.settings.mapContainerId), this.settings.mapConfig);

      // close all markers if zoomed
      this.map.addListener('zoom_changed', () => {
        this.closeAllMarkers();
      });

      // set all markers
      if (this.settings.data.length) {
        for (const property of this.settings.data) {

          let latNew = property.position.lat;
          let lngNew = property.position.lng;

          // check if there is already a marker on the position in question
          // than add a random threshold!
          // see: https://gis.stackexchange.com/questions/15436/google-markers-at-same-address-not-showing-all-markers
          for (const existingMarker of this.markers) {
            let latExist = existingMarker.position.lat;
            let lngExists = existingMarker.position.lng;
            let minThreshold = .999999;
            let maxThreshold = 1.000001;

            if (
              (latExist === latNew)
              && (lngExists === lngNew)
            ) {
              latNew = latNew * (Math.random() * (maxThreshold - minThreshold) + minThreshold);
              lngNew = lngNew * (Math.random() * (maxThreshold - minThreshold) + minThreshold);
              break;
            }
          }

          const AdvancedMarkerElement = new google.maps.marker.AdvancedMarkerElement({
            map: this.map,
            content: this.#buildContent(property),
            position: {
              lat: latNew,
              lng: lngNew
            },
            title: property.label,
          });

          // store marker in array
          this.markers.push(AdvancedMarkerElement);

          // close all markers and open current marker
          AdvancedMarkerElement.addListener('click', () => {
            this.closeAllMarkers(AdvancedMarkerElement);
            this.toggleMarker(AdvancedMarkerElement);
          });
        }

        // init filters
        this.filters = document.getElementsByClassName(this.settings.filterButtonClass);
        const self = this;
        if (this.filters) {
          Array.from(this.filters).forEach((filter) => {
            filter.addEventListener("click", function (e) {
              let target = e.target;
              self.toggleFilter(target);
            });
          });
        }

        // cluster marker
        this.clusterRenderer = {
          render({ count, position }, stats) {
            return new google.maps.marker.AdvancedMarkerElement({
              map: this.map,
              content: self.#buildClusterMarkerContent(count),
              position: position,
            });
          }
        }

        // add clustering
        this.markerClusterer = new MarkerClusterer({
          markers: this.markers,
          map: this.map,
          renderer: this.clusterRenderer
        });

        // re-center and reset zoom
        this.centerMap();
        this.initCanvas();

      } else {
        console.log('No marker-definitions given.');
      }
    } else {
      console.log('No container-id given.');
    }
  }


  /**
   *
   * @param property
   * @returns {HTMLDivElement}
   */
  #buildContent(property) {
    const content = document.createElement('div');
    content.setAttribute('data-categories', property.categories);

    const overlayContainer = document.getElementById(property.overlayContainerId);

    if (overlayContainer){
      content.innerHTML = overlayContainer.innerHTML;
      content.classList.add(overlayContainer.getAttribute('data-class'));
    }
    return content;
  }


  /**
   *
   * @returns {HTMLDivElement}
   * @param counter
   */
  #buildClusterMarkerContent(counter) {
    const content = document.createElement('div');
    const clusterContainer = document.getElementById(this.settings.clusterMarkerContainerId);

    if (clusterContainer){
      let innerHTML = clusterContainer.innerHTML;
      innerHTML = innerHTML.replaceAll('###counter###', counter);
      content.innerHTML = innerHTML;
      content.classList.add(clusterContainer.getAttribute('data-class'));
    }

    return content;
  }


  /**
   * Center map
   */
  centerMap() {

    // check if there boundaries to find
    if (this.settings.boundaryPositions.length > 1){

      let latLangBound = new google.maps.LatLngBounds();
      for (const position of this.settings.boundaryPositions) {
          latLangBound.extend(position);
      }
      this.map.fitBounds(latLangBound);

    } else {

      // re-center and reset zoom
      if (this.settings.mapConfig.center) {
        this.map.setCenter(this.settings.mapConfig.center);
      }

      if (this.settings.mapConfig.zoom) {
        this.map.setZoom(this.settings.mapConfig.zoom);
      }
    }
  }


  /**
   * Toggle given marker
   * @param callingFilter
   */
  toggleFilter(callingFilter) {

    for (let i = 0; i < this.filters.length; i++) {
      let filter = this.filters[i];
      filter.classList.remove('active');
    }

    callingFilter.classList.add('active');
    let category = callingFilter.getAttribute("data-category");
    this.hideAllMarkers();
    if (category) {
      this.showMarkersOfCategory(category);
    } else {
      this.showAllMarkers();
    }
  }


  /**
   * Toggle given marker
   * @param callingMarker
   */
  toggleMarker(callingMarker) {
    if (callingMarker.content.classList.contains('open')) {
      callingMarker.content.classList.remove('open');
      callingMarker.zIndex = null;
    } else {
      callingMarker.content.classList.add('open');
      callingMarker.zIndex = 1;
      this.map.panTo(callingMarker.position);
    }
  }


  /**
   * Closes all markers
   * @param callingMarker
   */
  closeAllMarkers(callingMarker = null) {
    for (let i = 0; i < this.markers.length; i++) {
      let marker = this.markers[i];
      if (callingMarker !== marker) {
        marker.content.classList.remove('open');
      }
    }
  }


  /**
   * Hide all markers on current map
   */
  hideAllMarkers() {
    for (let i = 0; i < this.markers.length; i++) {
      let marker = this.markers[i];
      marker.content.classList.remove('open');
      marker.map = null;
    }
    this.markerClusterer.removeMarkers(this.markers);
  }


  /**
   * Show all markers on current map
   */
  showAllMarkers () {
    for (let i = 0; i < this.markers.length; i++) {
      let marker = this.markers[i];
      marker.map = this.map;
    }

    this.markerClusterer.addMarkers(this.markers);

    // re-center and reset zoom
    this.centerMap();
  }


  /**
   * Show all markers of category.
   * @param category
   */
  showMarkersOfCategory (category) {

    let markers = [];
    for (let i= 0; i < this.markers.length; i++) {
      let marker = this.markers[i];
      let markerCategory = marker.content.getAttribute('data-categories');
      if (markerCategory) {
        let markerCategories = markerCategory.split(',');
        if (markerCategories.includes(category)) {
          marker.map = this.map;
          markers.push(marker);
        }
      }
    }

    this.markerClusterer.removeMarkers(this.markers);
    this.markerClusterer.addMarkers(markers);

    // re-center and reset zoom
    this.centerMap();
  }


  /**
   * Draws a canvas on top of the map
   * and cuts out the region defined in a GeoJSON object.
   *
   * The canvas stays fixed in the viewport (does not scroll with the page),
   * but re-renders its contents as the map is moved or zoomed.
   */
  initCanvas() {
    if (!this.settings.canvas.enabled || !this.settings.canvas.jsonCoordinates) return;

    const coords = this.settings.canvas.jsonCoordinates;
    const region = coords.map(([lng, lat]) => new google.maps.LatLng(lat, lng));
    const map = this.map;
    const fillStyle = this.settings.canvas.fillStyle;

    const overlay = new google.maps.OverlayView();
    overlay.onAdd = function () {
      const canvas = document.createElement("canvas");
      canvas.style.pointerEvents = "none";
      canvas.style.position = "absolute";
      canvas.style.top = "0";
      canvas.style.left = "0";
      canvas.style.transform = "translate(-50%, -50%)"; // Correction from center of map
      canvas.style.zIndex = "0";

      this.canvas = canvas;

      const panes = this.getPanes();
      panes.overlayLayer.appendChild(canvas);
    };

    overlay.draw = function () {
      const projection = this.getProjection();
      if (!projection || !this.canvas) return;

      const canvas = this.canvas;
      const ctx = canvas.getContext("2d");

      // Double canvas size to cover all movement directions during dragging
      const w = map.getDiv().offsetWidth;
      const h = map.getDiv().offsetHeight;
      canvas.width = w * 2;
      canvas.height = h * 2;

      // Shift drawing context so that map center is (0, 0) on the canvas
      ctx.setTransform(1, 0, 0, 1, w, h);

      // Fill entire canvas with semi-transparent canvas
      ctx.clearRect(-w, -h, canvas.width, canvas.height);
      ctx.fillStyle = fillStyle;
      ctx.fillRect(-w, -h, canvas.width, canvas.height);

      // Cut out the shape of Switzerland from the canvas
      ctx.globalCompositeOperation = "destination-out";
      ctx.beginPath();
      region.forEach((latlng, i) => {
        const pt = projection.fromLatLngToDivPixel(latlng);
        if (i === 0) ctx.moveTo(pt.x, pt.y);
        else ctx.lineTo(pt.x, pt.y);
      });
      ctx.closePath();
      ctx.fill();
      ctx.globalCompositeOperation = "source-over"
    };

    overlay.onRemove = function () {
      this.canvas?.remove();
      this.canvas = null;
    };

    overlay.setMap(map);

    // Trigger Redraw
    map.addListener("center_changed", () => overlay.draw());
    map.addListener("zoom_changed", () => overlay.draw());
    map.addListener("resize", () => overlay.draw());
  }






}
