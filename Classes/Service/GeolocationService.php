<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Madj2k\GadgetoGoogle\Domain\DTO\Location;
use Madj2k\GadgetoGoogle\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * GeolocationService
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GeolocationService implements SingletonInterface
{

    /**
     * @const string
     */
    const GOOGLE_API_URL = 'https://maps.google.com/maps/api/geocode/json';


    /**
     * @const string
     */
    const API_CALL_TYPE_LNGLAT = 'latlng';


    /**
     * @const string
     */
    const API_CALL_TYPE_ADDRESS = 'address';


    /**
     * @var array
     */
    protected array $settings = [];


    /**
     * @var \Madj2k\GadgetoGoogle\Domain\DTO\Location|null
     */
    protected ?Location $location = null;


    /**
     * @var string
     */
    protected string $rawQuery = '';


    /**
     * @var string
     */
    protected string $lastApiCall = '';


    /**
     * @var array|null
     */
    protected ?array $lastApiResponse = null;


    /**
     * @var string
     */
    protected string $apiCallType = '';


    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * Constructor
     *
     * @param array $settings
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
        $this->location = new Location($settings);
    }


    /**
     * Set settings
     *
     * @param array $settings
     * @return void
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }


    /**
     * Get the location
     *
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }


    /**
     * Sets the location
     *
     * @param Location $location
     * @return void
     */
    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }


    /**
     * Get rawQuery
     *
     * @return string
     */
    public function getRawQuery(): string
    {
        return $this->rawQuery;
    }


    /**
     * Set rawQuery
     *
     * @param string $rawQuery
     * @return void
     */
    public function setRawQuery(string $rawQuery): void
    {
        $this->rawQuery = $rawQuery;
    }


    /**
     * Gets the apiCallType
     *
     * @return string
     */
    public function getApiCallType(): string
    {
        if (! $this->apiCallType) {
            if ($this->getLocation()->getLongitude() && $this->getLocation()->getLatitude()) {
                $this->apiCallType = self::API_CALL_TYPE_LNGLAT;
            } else {
                $this->apiCallType = self::API_CALL_TYPE_ADDRESS;
            }
        }

        return $this->apiCallType;
    }


    /**
     * Sets the apiCallType
     *
     * @param  string $type
     * @return self
     */
    public function setApiCallType(string $type): self
    {
        $this->apiCallType = $type;
        return $this;
    }


    /**
     * Gets the last API-call
     *
     * @return string
     */
    public function getLastApiCall(): string
    {
        return $this->lastApiCall;
    }


    /**
     * Gets the last API-result
     *
     * @return array|null
     */
    public function getLastApiResponse(): ?array
    {
        return $this->lastApiResponse;
    }


    /**
     * Fetches the geodata via Google API
     * Set a normal address or a pair of longitude and latitude
     * Attention: Works with "long + lat" OR "postalCode" OR "address" (address is most generally and can include an postal code)
     * Hint: Country is optional. Needed if there are only a postal code is given. "de" for germany is default.
     * -> Both is possible: "DE" or "germany"
     *
     * @return bool
     */
    public function fetchData(): bool
    {
        $apiQueryString = [];
        try {

             /** @var $logger \TYPO3\CMS\Core\Log\Logger */
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)
                ->getLogger(__CLASS__);

            // take raw query or concrete and structured location-data
            if ($this->getRawQuery()) {
                if ($this->getApiCallType() == self::API_CALL_TYPE_LNGLAT) {
                    $apiQueryString = $this->getRawQuery();
                } else {
                    $apiQueryString = $this->getRawQuery() . ', ' . $this->getLocation()->getCountry();
                }
            } else {
                if ($this->getApiCallType() == self::API_CALL_TYPE_LNGLAT) {
                    $apiQueryString = $this->getLocation()->getLongLatAsString();
                } else {
                    $apiQueryString = $this->getLocation()->getAddressAsString();
                }
            }

            if (! $apiCall = $this->buildApiCallUrl($apiQueryString)) {
                $this->logger->info(
                    sprintf('Google API call for location "%s" has already been successfully completed and is therefore ignored (%s).',
                        $apiQueryString,
                        $this->lastApiCall,
                    )
                );
                return true;
            }

            // do API-call via cURL
            $httpReturnCode = $this->doCurlRequest($apiCall);

            // if response is "OK", then set received data to object
            if (
                ($httpReturnCode == 200)
                && (isset($this->lastApiResponse['status']))
                && ($this->lastApiResponse['status'] == 'OK')
            ){

                if ($this->insertDataFromApiCall()) {
                    $this->logger->info(
                        sprintf('Google API call for location "%s" successfully completed (%s).',
                            $apiQueryString,
                            $this->lastApiCall,
                        )
                    );

                    return true;

                } else {
                    throw new Exception(
                        sprintf(
                            'Incomplete API response: %s',
                            print_r($this->lastApiResponse, true),
                        ),
                        1714934774
                    );
                }
            }

            throw new Exception(
                sprintf(
                    '%s: %s',
                    $this->lastApiResponse['status'] ?? $httpReturnCode,
                    $this->lastApiResponse['error_message'] ?? ''
                ),
                1714934775
            );


        } catch (\Exception $e) {

            $this->logger->error(
                sprintf('Google API for location "%s" call failed (%s). Reason: %s',
                    $apiQueryString,
                    $this->lastApiCall,
                    $e->getMessage()
                )
            );
        }

        return false;
    }


    /**
     * @param string $url
     * @return int
     * @throws \Madj2k\GadgetoGoogle\Exception
     */
    protected function doCurlRequest (string $url): int
    {
        $httpReturnCode = 500;
        $ch = null;
        try {

            // set up context if proxy is used
            $ch = curl_init();
            if (isset($this->settings['proxy'])) {

                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL , 1);
                curl_setopt($ch, CURLOPT_PROXY, $this->settings['proxy']);
                if (isset($this->settings['proxyPort'])) {
                    curl_setopt($ch, CURLOPT_PROXYPORT, $this->settings['proxyPort']);
                }
                if (isset($this->settings['proxyUsername'])) {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->settings['proxyUsername'] . ':' . $this->settings['proxyPassword']);
                }
            }

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $apiResponse = curl_exec($ch);
            $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->lastApiCall = $url;

            // check if response can be interpreted as JSON
            if (
                ($apiResponse !== false)
                && (is_string($apiResponse))
            ) {
                $this->lastApiResponse = json_decode($apiResponse, true);
            } else {
                $errorMessage = curl_error($ch);
                $this->lastApiResponse = ['error_message' => $errorMessage];
            }
            curl_close($ch);

        } catch(Exception $e) {

            throw new Exception(
                sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(),
                    $e->getMessage()
                ),
                1714934776
            );

        } finally {
            // Close curl handle unless it failed to initialize
            if (is_resource($ch)) {
                curl_close($ch);
            }
        }

        return $httpReturnCode;
    }


    /**
     * Build url for API call
     *
     * @param string $apiQueryString
     * @return string
     * @throws \Madj2k\GadgetoGoogle\Exception
     */
    protected function buildApiCallUrl(string $apiQueryString): string
    {
        if (empty($apiQueryString)) {
            throw new Exception(
                'No valid location given. You must specify an address or longitude and latitude.',
                1714934773
            );
        }

        $registry = GeneralUtility::makeInstance(Registry::class);
        $googleMapsConfig = $registry->get(
            'gadgeto_google',
            'googleMapsConfig',
        );

        // build url
        $destinationString = urlencode($apiQueryString);
        $apiCall = self::GOOGLE_API_URL . '?sensor=false&' . $this->getApiCallType() . '={' . $destinationString . '}';
        if ($this->getApiCallType() == self::API_CALL_TYPE_LNGLAT) {
            $apiCall = self::GOOGLE_API_URL . '?sensor=false&' . $this->getApiCallType() . '=' . $destinationString ;
        }

        // check for additional api key (not mandatory)
        if (isset($googleMapsConfig['apiKey'])) {
            $apiCall .= "&key=" . $googleMapsConfig['apiKey'];
        }

        // check for current language
        if (
            ($request = $this->getRequest())
            && ($language = $request->getAttribute('language'))
            && ($locale = $language->getLocale())
        ) {
            $apiCall .= "&language=" . $locale;
        }

        if ($this->lastApiCall == $apiCall) {
            return '';
        }

        return $apiCall;
    }


    /**
     * Inject data from API call
     *
     * @return bool
     */
    protected function insertDataFromApiCall(): bool
    {
        if (
            (isset($this->lastApiResponse['results']))
            && (isset($this->lastApiResponse['results'][0]))
        ){

            if (
                (isset($this->lastApiResponse['results'][0]['geometry']))
                && (isset($this->lastApiResponse['results'][0]['geometry']['location']))
                && (isset($this->lastApiResponse['results'][0]['geometry']['location']['lat']))
                && (isset($this->lastApiResponse['results'][0]['geometry']['location']['lng']))
            ) {

                $this->getLocation()->setLatitude(
                    filter_var(
                        $this->lastApiResponse['results'][0]['geometry']['location']['lat'],
                        FILTER_VALIDATE_FLOAT
                    )
                );

                $this->getLocation()->setLongitude(
                    filter_var(
                        $this->lastApiResponse['results'][0]['geometry']['location']['lng'],
                        FILTER_VALIDATE_FLOAT
                    )
                );
            }

            // additional data
            if (isset($this->lastApiResponse['results'][0]['address_components'])) {

                $addressComponents = $this->lastApiResponse['results'][0]['address_components'];

                if (is_array($addressComponents)) {
                    foreach ($addressComponents as $component) {

                        switch ($component['types'][0]) {
                            case 'route':
                                $this->getLocation()->setStreet($component['long_name']);
                                break;
                            case 'street_number':
                                $this->getLocation()->setStreetNumber($component['long_name']);
                                break;
                            case 'postal_code':
                                $this->getLocation()->setPostalCode($component['long_name']);
                                break;
                            case 'locality':
                                $this->getLocation()->setCity($component['long_name']);
                                break;
                            case 'country':
                                $this->getLocation()->setCountry($component['short_name']);
                                break;
                        }
                    }
                }
            }

            return true;
        }

        return false;
    }


    /**
     * Inject data from record
     *
     * @param string $table Tablename of data record
     * @param mixed $uid Uid of data record
     * @param array $fieldArray Current fieldArray from DataHandler
     * @param array $keyMap Array which maps the object-keys to the API-keys
     * @return bool
     */
    public function insertDataFromRecord(
        string $table,
        mixed $uid,
        array &$fieldArray,
        array $keyMap = [
            'street' => 'street', 'street_number' => 'street_number', 'zip' => 'zip', 'city' => 'city',
            'country' => 'country', 'address_addition_api' => 'address_addition_api', 'manual_lng_lat' => 'manual_lng_lat'
        ]
    ): bool {

        $fieldArrayInternal = $fieldArray;

        // do not init data, if longitude or latitude are set because then we most likely have an import going on
        if (isset($fieldArrayInternal['longitude']) || isset($fieldArrayInternal['latitude'])) {
            return false;
        }

        // check if there is a key match to check if relevant fields from fieldArray have been changed
        $doInit = false;
        foreach ($keyMap as $key => $map) {
            if (isset($fieldArrayInternal[$key])) {
                $doInit = true;
                break;
            }
        }

        if ($doInit) {

            // check for existing TCA-fields!
            $keyMapSanitized = [];
            foreach ($keyMap as $key => $map) {
                if (isset($GLOBALS['TCA'][$table]['columns'][$key])) {
                    $keyMapSanitized[$key] = $map;
                }
            }

            // try to load the record completely, because fieldArray only contains changes
            // and merge the changes into it
            if (is_numeric($uid)) {
                $fieldArrayInternal = array_merge(BackendUtility::getRecord(
                    $table,
                    $uid,
                    implode(',', array_keys($keyMapSanitized))),
                    $fieldArrayInternal
                );
            }

            // if geolocation is to be set manually, skip from here!
            if (isset($fieldArrayInternal['manual_lng_lat'])) {
                return false;
            }

            $this->location = new Location($this->settings);
            foreach ($keyMapSanitized as $key => $map) {
                $setter = 'set' . GeneralUtility::underscoredToUpperCamelCase($map);
                if (method_exists($this->getLocation(), $setter)) {
                    if (!empty($fieldArrayInternal[$key])){
                        $this->getLocation()->$setter($fieldArrayInternal[$key]);
                    }
                }
            }

            return true;
        }

        return false;
    }


    /**
     * Get request object
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
