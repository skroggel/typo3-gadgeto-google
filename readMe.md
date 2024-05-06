# Gadgeto Google
Provides Utilities and ViewHelpers for Google Geolocation API and Google Maps

## Features
* Gets geolocation data from Google Geolocation API when a location record is saved in the backend
* Displays a Google Map of all location records with category filters
* Consent overlay for Google Map (GDPR)

## Installation
Just install the extension and include the TypoScript. 
At the moment there is nothing to configure.

## Usage in your own extension
### General
* Your location-model has either to
  * implement the FilterableInterface and use the FilterableTrait OR
  * extend the Location-model.
* If you use the FilterableTrait, make sure you add the corresponsing TCA-definitions and database-fields (see: **ext_tables.sql** and **TCA/tx_gadgetogoogle_domain_model_location.php**)

### Load geodata from API when location record is stored
* First you need to register a hook to DataHandler in your **ext_localconf.php**
```
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['example'] =
   \My\Example\Hooks\TceMainHooks::class;
```
* Then you can call the Geolocation-Service in your hook. See **Classes/Hooks/TceMainHooks.php** for an example
