<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Domain\Model;

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
 * Class AddressInterface
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface AddressInterface 
{

    /**
     * Returns the company
     *
     * @return string
     */
    public function getCompany(): string;


    /**
     * Sets the company
     *
     * @param string $company
     * @return void
     */
    public function setCompany(string $company): void;


    /**
     * Returns the street
     *
     * @return string
     */
    public function getStreet(): string;


    /**
     * Sets the street
     *
     * @param string $street
     * @return void
     */
    public function setStreet(string $street): void;


    /**
     * Returns the streetNumber
     *
     * @return string
     */
    public function getStreetNumber(): string;


    /**
     * Sets the streetNumber
     *
     * @param string $streetNumber
     * @return void
     */
    public function setStreetNumber(string $streetNumber): void;


    /**
     * Returns the zip
     *
     * @return string
     */
    public function getZip(): string;


    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip(string $zip): void;


    /**
     * Returns the city
     *
     * @return string
     */
    public function getCity(): string;


    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity(string $city): void;

}
