# Gadgeto Google

**Gadgeto Google** is a powerful TYPO3 extension that provides utilities for integrating the
Google Geolocation API and Google Maps into your TYPO3 installation.
It allows automatic geolocation of records, map display with filters, consent management,
and full customization via Fluid templates.

---

## 🚀 Features

- Automatically fetches geolocation data (latitude/longitude) from the Google Geolocation API when a record is saved in the backend.
- Displays an interactive Google Map with:
    - Address-based search functionality
    - Filterable markers via categories
    - Marker clustering to handle overlapping locations
    - Clickable markers with custom detail overlays
    - Ability to add **custom overlays** to highlight regions (e.g., specific countries)
- Fully customizable via Fluid templates
- Can be configured to use a single location as center of the map or show all definied locations at once
- List of all locations, hierarchically ordered by categories
- GDPR-compliant consent overlay for map loading
- Backend-ordered list of locations by category
- Extendable with your own models and repositories
- PageTS and TypoScript configuration for dynamic behavior

---
## ❗Breaking Changes


**IMPORTANT - BREAKING CHANGES v13:**

New template-structure. If you need to use the old templates, set the following TypoScript config via constants:

```constants
plugin.tx_gadgetogoogle {

	view {
		# cat=plugin.tx_gadgetogoogle/file; type=string; label=Path to template root (FE)
		templateRootPath = EXT:gadgeto_google/Resources/Private/v12/Templates/

		# cat=plugin.tx_gadgetogoogle/file; type=string; label=Path to template partials (FE)
		partialRootPath = EXT:gadgeto_google/Resources/Private/v12/Partials/

		# cat=plugin.tx_gadgetogoogle/file; type=string; label=Path to template layouts (FE)
		layoutRootPath = EXT:gadgeto_google/Resources/Private/v12/Layouts/
	}
}
```

**IMPORTANT - BREAKING CHANGES v12:**

Add the following classes to your HTML elements:

- `js-gadgetogoogle-map-consent-btn`: Consent button that enables the map
- `js-gadgetogoogle-map-filter-btn`: Filter buttons interacting with the map

---

## 📦 Installation

1. Install the extension using the Extension Manager or Composer.
2. Include the **static TypoScript** or use the provided **page setup** template.
3. The extension requires `sjbr/static-info-tables`. Import its tables through the Extension Manager to enable proper country support.

---

## 🔑 Google API Configuration

You need **two separate Google API keys** to use all features:

### 1. Geolocation API Key
Used to fetch coordinates based on addresses when saving a record in the backend.

- **Required access**: Geolocation API
- **Restriction type**: IP address (for backend security)

### 2. Maps JavaScript API Key
Used to render the map in the frontend.

- **Required access**: Maps JavaScript API
- **Restriction type**: HTTP referrer (frontend usage)

### 3. Google Maps ID
For enhanced styling and display behavior.

- **Map type**: JavaScript
- **Style type**: Raster map

More details:
- [Get API Key](https://developers.google.com/maps/documentation/javascript/get-api-key?hl=en)
- [Get Map ID](https://developers.google.com/maps/documentation/get-map-id?hl=en)

---

## ⚙️ Extension Configuration

Go to **System → Settings → Extension Configuration** in the TYPO3 backend. Available global configuration options include:

### `apiHookTables`
Comma-separated list of database tables that should trigger a call to the Google Geolocation API to populate latitude and longitude.

> Ensure these tables have all necessary fields (see `ext_tables.sql`, especially `tx_gadgetogoogle_domain_model_location`).

### `defaultCountry`
Defines a default/fallback country used when resolving address queries via the search bar. This value can be overridden in the plugin's FlexForm configuration.

### `removeFields`
Specifies which fields of the location records should be hidden in the backend. Provide a comma-separated list of field names.

### `pluginsWithHeader`
Controls which plugins display the "header" field in the backend form.

---

## 🛠 TypoScript Configuration

Place the following configuration into your TypoScript template to control frontend behavior:

```typoscript
plugin.tx_gadgetogoogle.settings {
  maxSearchRadius = 10000
  maxSearchDisplayRadius = 5000
  defaultCountry = DE
}
```

These options control:

- **maxSearchRadius**: Maximum distance (in meters) for address-based search.
- **maxSearchDisplayRadius**: Maximum radius for displaying matching markers.
- **defaultCountry**: As above, sets default country code.

If you're using a proxy server, you can also configure cURL to respect proxy settings to allow external API requests.

---

## 🧩 FlexForm Customization via PageTS

To extend the available layout options in your plugin FlexForms:

```typoscript
// Important: You have to specifiy the extension-plugin (e.g. gadgetogoogle_map)
// AND the tab in the flexform (e.g. view) correctly
TCEFORM.tt_content.pi_flexform.gadgetogoogle_map.view.settings\.layout {

    // add new option "List"
    addItems.list = List

    // Override label of existing option "default"
    altLabels.default = Slot

    // remove option "big"
    removeItems = big
}

TCEFORM.tt_content.pi_flexform.gadgetogoogle_map.list.settings\.paginationStyle {

    // remove option "More"
    removeItems = More
}

TCEFORM.tt_content.pi_flexform.gadgetogoogle_map.list.settings\.listStyle {

    // remove option "Category"
    removeItems = Category
}

```

---

## 🎨 Category Style Customization via PageTS

To define selectable styles for categories:

```typoscript
TCEFORM.sys_category.tx_gadgetogoogle_style {

    // add new option "Layout.1"
    addItems.list = Layout-1

    // Override label of existing option "default"
    altLabels.default = Slot

    // remove option "big"
    removeItems = big
}
```

---

## 🧩 Restrict Selectable Categories via PageTS

To limit the category selection tree in the backend for locations:

```typoscript
TCEFORM.tx_gadgetogoogle_domain_model_location.categories {
    config {
        treeConfig {
            startingPoints = 7 // parent category to start from
            appearance.nonSelectableLevels = 0,1 // make root and parent-category not selectable
        }
    }
}
```

---

## 🔄 Integration Into Your Own Extensions

You can integrate the geolocation functionality into your custom extensions in two ways:

### Option 1: Inheritance (Recommended)

Extend your model from the `Location` class provided by this extension. This gives you all geolocation logic out of the box.
👉 [Learn how to extend Extbase models in TYPO3](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/HowTo/ExtendExtbaseModel/Index.html)

### Option 2: Trait + Interface

Implement `\Gadgeto\GadgetoGoogle\Domain\FilterableInterface` and use `FilterableTrait` in your model class.

> ⚠ You are responsible for:
> - Defining required TCA and DB fields (see `TCA/tx_gadgetogoogle_domain_model_location.php`)
> - Writing your own repositories

---

## 📍 Trigger Geolocation on Record Save

### Recommended Method

Add your table to `apiHookTables` in the extension configuration. This automatically triggers geolocation when saving a record.

### Manual Hook (Alternative)

If you prefer a custom hook, register it in `ext_localconf.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['myhook'] =
   \Vendor\Extension\Hooks\TceMainHooks::class;
```

In your hook class, call the Geolocation service:

```php
use Gadgeto\GadgetoGoogle\Service\GeolocationService;
```

See the example in `Classes/Hooks/TceMainHooks.php`.

---

## 👥 GDPR Consent Overlay

To comply with GDPR (DSGVO), a consent dialog must be shown before loading Google Maps.
This overlay is part of the extension.

There is a config-file for klaro-consent-manager included. You can use it with `ext:klarokartie` by adding the following lines to your site-config (yaml):
```
klarokratie:
  klaro:
    config: EXT:klarokratie/Resources/Public/Config/KlaroConfigMinimal.js
    includes:
      - EXT:gadgeto_google/Resources/Public/Klarokratie/Includes/GoogleMaps.js
```

If you want to use a different consent manager, you can use the following JavaScript-Events depending on a given or revoked consent:
```
document.dispatchEvent(new CustomEvent('gadgetoGoogle:consent:given'));
```
```
document.dispatchEvent(new CustomEvent('gadgetoGoogle:consent:revoked'));
```
---

## 📚 Resources

- [Google Maps JavaScript API Documentation](https://developers.google.com/maps/documentation/javascript)
- [Google Geolocation API Overview](https://developers.google.com/maps/documentation/geolocation/overview)
- [TYPO3 Coding Guidelines](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/CodingGuidelines/Index.html)
