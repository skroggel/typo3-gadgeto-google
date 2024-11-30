# Gadgeto Google
Provides Utilities and ViewHelpers for Google Geolocation API and Google Maps

**IMPORTANT - BREAKING CHANGE:**

You have to add the following CSS-classes to your custom HTML:
* ```js-gadgetogoogle-map-consent-btn``` on your consent button
* ```js-gadgetogoogle-map-filter-btn``` on your filter buttons

## Features
* Gets geolocation data from Google Geolocation API when a location record is saved in the backend
* Displays a Google Map of all location records with category filters
* Allows a search by address and displays results on the map
* Automatically clusters overlapping markers on the map
* Shows detail information on click on a marker on the map
* Markers and detail-overlay can be fully customized via Fluid-Templates
* Can be configured to use a single location as center of the map or show all definied locations at once
* Consent overlay for Google Map (GDPR)

## Installation
Just install the extension and include the TypoScript.

You will need a Google API Key for beeing aple to use the Maps API (see: https://developers.google.com/maps/documentation/javascript/get-api-key?hl=de)
and a Map Key (see: https://developers.google.com/maps/documentation/get-map-id?hl=de).

## Possible global configurations (System -> Settings -> Extension Configuration)
You can configure the following three things via the extension configuration (System -> Settings -> Extension Configuration):

* **apiHookTables**: here you can define the tables names that trigger a call to the geolocation API of Google in order to determine the longitude and latiude of the record.
Make sure that the tables defined here have all necessary fields (see [ext_tables.sql]() -> tx_gadgetogoogle_domain_model_location)
* **defaultCountry**: with this setting you define the implicit country that is used to find addresses if a user uses the address search.
This settings is a fallback-value that can be overriden in the plugin.
* **removeFields**: This setting allows you to customize the fields of the location-records that are shown in the backend.
You can define a comma-separated list of fields to remove from the TCA.

## Possible other configurations (TypoScript)
* **maxSearchRadius**: defines the maximum radius of a search when a user searchs for an address
* **maxSearchDisplayRadius:** defines the maximum radius for the display of results on the map when a user searchs for an address
* **defaultCountry**: see above

If you use a proxy, you can also set the relevant proxy-settings for cURL so that the necessary API-calls can be executed.

## Usage in your own extension
### General
Your location-model has either to
* extend this extension the "old-school-way" (recommended)
* implement the FilterableInterface and use the FilterableTrait

If you use the FilterableTrait, make sure you add the corresponsing TCA-definitions and database-fields (see: **ext_tables.sql** and **TCA/tx_gadgetogoogle_domain_model_location.php**)
**However, if you use this minimalistic approach you have to write your repositories and models yourself!**

### Load geodata from API when location record is stored
You can either register your own hook OR simply add your table to the extension-configuration in the backend (System -> Settings -> Extension Settings)
**Adding your table(s) to the extension-configuration is the recommended way to go**, nevertheless if you opt for your own hook
you need to register a hook to DataHandler in your **ext_localconf.php** first
```
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['example'] =
   \My\Example\Hooks\TceMainHooks::class;
```
Then you can call the Geolocation-Service in your hook. See **[Classes/Hooks/TceMainHooks.php]()** for an example
