<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Traits;

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
 * Class AddressTrait
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
trait AddressTrait
{

    /**
     * @var string
     */
    protected string $street = '';


    /**
     * @var string
     */
    protected string $streetNumber = '';


    /**
     * @var string
     */
    protected string $zip = '';


    /**
     * @var string
     */
    protected string $city = '';


    /**
     * @var string
     */
    protected string $country = '';


    /**
     * @var string
     */
    protected string $addressAdditionApi = '';


    /**
     * Returns the street
     *
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }


    /**
     * Sets the street
     *
     * @param string $street
     * @return void
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }


    /**
     * Returns the streetNumber
     *
     * @return string
     */
    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }


    /**
     * Sets the streetNumber
     *
     * @param string $streetNumber
     * @return void
     */
    public function setStreetNumber(string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }


    /**
     * Returns the zip
     *
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }


    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }


    /**
     * Returns the city
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }


    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
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
     * @return void
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }


    /**
     * Returns the addressAdditionApi
     *
     * @return string
     */
    public function getAddressAdditionApi(): string
    {
        return $this->addressAdditionApi;
    }


    /**
     * Sets the addressAdditionApi
     *
     * @param string $addressAdditionApi
     * @return void
     */
    public function setAddressAdditionApi(string $addressAdditionApi): void
    {
        $this->addressAdditionApi = $addressAdditionApi;
    }
}
