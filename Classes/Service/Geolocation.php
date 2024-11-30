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


/**
 * Geolocation
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @deprecated use GeolocationService instead
 */
class Geolocation extends GeolocationService
{

    /**
     * Returns the longitude
     *
     * @return float $longitude
     */
    public function getLongitude(): float
    {
        return $this->location->getLongitude();
    }


    /**
     * Sets the longitude
     *
     * @param float $longitude
     * @return void
     */
    public function setLongitude(float $longitude): void
    {
        $this->location->setLongitude($longitude);
    }


    /**
     * Returns the latitude
     *
     * @return float $latitude
     */
    public function getLatitude(): float
    {
        return $this->location->getLatitude();
    }


    /**
     * Sets the latitude
     *
     * @param float $latitude
     * @return void
     */
    public function setLatitude(float $latitude): void
    {
        $this->location->setLatitude($latitude);
    }


    /**
     * Returns the distance
     *
     * @return float $distance
     */
    public function getDistance(): float
    {
        return $this->location->getDistance();
    }


    /**
     * Sets the distance
     *
     * @param float $distance
     * @return void
     */
    public function setDistance(float $distance): void
    {
        $this->location->setDistance($distance);
    }


    /**
     * Returns the street
     *
     * @return string
     */
    public function getStreet(): string
    {
        return $this->location->getStreet();
    }


    /**
     * Sets the street
     *
     * @param string $street
     * @return void
     */
    public function setStreet(string $street): void
    {
        $this->location->setStreet($street);
    }


    /**
     * Returns the streetNumber
     *
     * @return string
     */
    public function getStreetNumber(): string
    {
        return $this->location->getStreetNumber();
    }


    /**
     * Sets the streetNumber
     *
     * @param string $streetNumber
     * @return void
     */
    public function setStreetNumber(string $streetNumber): void
    {
        $this->location->setStreetNumber($streetNumber);
    }


    /**
     * Returns the zip
     *
     * @return string
     */
    public function getZip(): string
    {
        return $this->location->getZip();
    }


    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip(string $zip): void
    {
        $this->location->setZip($zip);
    }


    /**
     * Returns the city
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->location->getCity();
    }


    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity(string $city): void
    {
        $this->location->setCity($city);
    }


    /**
     * Returns the country
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->location->getCountry();
    }


    /**
     * Sets the country
     *
     * @param string $country
     * @return void
     */
    public function setCountry(string $country): void
    {
        $this->location->setCountry($country);
    }


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
        $this->setSettings($settings);
        return $this->fetchData();
    }

}
