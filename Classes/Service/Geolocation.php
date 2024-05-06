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

use Madj2k\GadgetoGoogle\Exception;
use Madj2k\GadgetoGoogle\Traits\GeoPositionTrait;
use Madj2k\GadgetoGoogle\Traits\AddressTrait;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Geolocation
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Geolocation implements SingletonInterface
{

    use GeoPositionTrait;
    use AddressTrait;

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
     * @var string
     */
    protected string $country = 'Germany';


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
     * Alias of getZip
     *
     * @return string $postalCode
     */
    public function getPostalCode(): string
    {
        return $this->getZip();
    }


    /**
     * Alias of setZip
     *
     * @param string $postalCode
     * @return void
     */
    public function setPostalCode(string $postalCode): void
    {
       $this->setZip($postalCode);
    }


    /**
     * Returns the country
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }


    /**
     * Sets the country
     *
     * @param string $country
     * @return self
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }


    /**
     * Gets the apiCallType
     *
     * @return string
     */
    public function getApiCallType(): string
    {
        if (! $this->apiCallType) {
            if ($this->getLongitude() && $this->getLatitude()) {
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
        array $keyMap = ['street' => 'street', 'street_number' => 'street_number', 'zip' => 'zip', 'city' => 'city']
    ): bool {

        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)
            ->getLogger(__CLASS__);

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
                continue;
            }
        }

        if ($doInit) {

            // try to load the record completely, because fieldArray only contains changes
            // and merge the changes into it
            if (is_numeric($uid)) {
                $fieldArrayInternal = array_merge(BackendUtility::getRecord(
                    $table,
                    $uid,
                    implode(',', array_keys($keyMap))),
                    $fieldArrayInternal
                );
            }

            foreach ($keyMap as $key => $map) {
                $setter = 'set' . GeneralUtility::underscoredToUpperCamelCase($map);
                if (method_exists($this, $setter)) {
                    if (
                        (isset($fieldArrayInternal[$key]))
                        && (!empty($fieldArrayInternal[$key]))
                    ){
                        $this->$setter($fieldArrayInternal[$key]);
                    }
                }
            }

            return true;
        }

        return false;
    }


    /**
     * Fetches the geodata via Google API
     * Set a normal address or a pair of longitude and latitude
     * Attention: Works with "long + lat" OR "postalCode" OR "address" (address is most generally and can include an postal code)
     * Hint: Country is optional. Needed if there are only a postal code is given. "de" for germany is default.
     * -> Both is possible: "DE" or "germany"
     *
     * @param array $settings
     * @return bool
     */
    public function fetchData(array $settings = []): bool
    {
        $destinationArray = [];
        try {

             /** @var $logger \TYPO3\CMS\Core\Log\Logger */
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)
                ->getLogger(__CLASS__);

            $destinationArray = $this->buildDestinationArray();
            if (! $apiCall = $this->buildApiCallUrl($destinationArray)) {
                $this->logger->info(
                    sprintf('Google API call for location "%s" has already been successfully completed and is therefore ignored (%s).',
                        implode(',', $destinationArray),
                        $this->lastApiCall,
                    )
                );
                return true;
            }

            // do API-call via cURL
            $httpReturnCode = $this->doCurlRequest($apiCall, $settings);

            // if response is "OK", then set received data to object
            if (
                ($httpReturnCode == 200)
                && (isset($this->lastApiResponse['status']))
                && ($this->lastApiResponse['status'] == 'OK')
            ){

                if ($this->insertDataFromApiCall()) {
                    $this->logger->info(
                        sprintf('Google API call for location "%s" successfully completed (%s).',
                            implode(',', $destinationArray),
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
                    implode(',', $destinationArray),
                    $this->lastApiCall,
                    $e->getMessage()
                )
            );
        }

        return false;
    }


    /**
     * @param string $url
     * @param array $settings
     * @return int
     */
    protected function doCurlRequest (string $url, array $settings): int
    {
        $httpReturnCode = 500;
        $ch = null;
        try {

            // set up context if proxy is used
            $ch = curl_init();
            if (isset($settings['proxy'])) {

                curl_setopt($ci, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ci, CURLOPT_HTTPPROXYTUNNEL , 1);
                curl_setopt($ci, CURLOPT_PROXY, $settings['proxy']);
                if (isset(settings['proxyPort'])) {
                    curl_setopt($ci, CURLOPT_PROXYPORT, $settings['proxyPort']);
                }
                if (isset($settings['proxyUsername'])) {
                    curl_setopt($ci, CURLOPT_PROXYUSERPWD, $settings['proxyUsername'] . ':' . $settings['proxyPassword']);
                }
            }

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $apiResponse = curl_exec($ch);
            $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->lastApiCall = $url;
            $errorMessage = '';

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
     * Build destination array
     * @return array
     */
    protected function buildDestinationArray(): array
    {
        $destinationArray = [];

        // if long AND lat is given (only one of these doesn't work)
        if ($this->getApiCallType() == self::API_CALL_TYPE_LNGLAT) {
            $destinationArray[] = $this->getLongitude();
            $destinationArray[] = $this->getLatitude();
        }

        // use address
        if ($this->getApiCallType() == self::API_CALL_TYPE_ADDRESS) {
            if ($this->getStreet() && $this->getStreetNumber()) {
                $destinationArray[] = $this->getStreet() . ' ' . $this->getStreetNumber();
            }
            if ($this->getStreet() && !$this->getStreetNumber()) {
                $destinationArray[] = $this->getStreet();
            }
            if ($this->getPostalCode()) {
                $destinationArray[] = $this->getPostalCode();
            }
            if ($this->getCity()) {
                $destinationArray[] = $this->getCity();
            }

            // we don't want to get the middle of the country as coordinates
            if (count($destinationArray)) {
                if ($this->getCountry()) {
                    $destinationArray[] = $this->getCountry();
                }
            }
        }

        return $destinationArray;
    }


    /**
     * Build url for API call
     * @param array $destinationArray
     * @return array
     */
    protected function buildApiCallUrl(array $destinationArray): string
    {
        if (!count($destinationArray)) {
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
        $destinationString = urlencode(implode(', ', $destinationArray));
        $apiCall = self::GOOGLE_API_URL . '?sensor=false&' . $this->getApiCallType() . '={' . $destinationString . '}';
        if ($this->getApiCallType() == self::API_CALL_TYPE_LNGLAT) {
            $apiCall = self::GOOGLE_API_URL . '?sensor=false&' . $this->getApiCallType() . '=' . $destinationString ;
        }

        // check for additional api key (not mandatory)
        if (isset($googleMapsConfig['apiKey'])) {
            $apiCall .= "&key=" . $googleMapsConfig['apiKey'];
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

                $this->setLatitude(
                    filter_var(
                        $this->lastApiResponse['results'][0]['geometry']['location']['lat'],
                        FILTER_VALIDATE_FLOAT
                    )
                );

                $this->setLongitude(
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
                                $this->setStreet($component['long_name']);
                                break;
                            case 'street_number':
                                $this->setStreetNumber($component['long_name']);
                                break;
                            case 'postal_code':
                                $this->setPostalCode($component['long_name']);
                                break;
                            case 'locality':
                                $this->setCity($component['long_name']);
                                break;
                            case 'country':
                                $this->setCountry($component['long_name']);
                                break;
                        }
                    }
                }
            }

            return true;
        }

        return false;
    }
}
