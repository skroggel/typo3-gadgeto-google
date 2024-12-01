<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Domain\DTO;

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

use Madj2k\GadgetoGoogle\Traits\AddressTrait;
use Madj2k\GadgetoGoogle\Traits\GeoPositionTrait;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Location
 *
 * @author Steffen Kroggel <mail@steffenkroggel.de>
 * @copyright Steffen Kroggel <mail@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class Location
{

    use GeoPositionTrait;
    use AddressTrait;


    /**
     * @var array
     */
    protected array $settings = [];


    /**
     * Constructor
     *
     * @param array $settings
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
        try {
            $this->initCountry();
        } catch (\Exception $e) {
            // do nothing
        }
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
     * Get longitude and latitude as string
     *
     * @return string
     */
    public function getLongLatAsString(): string
    {
        $destinationArray = [];
        $destinationArray[] = $this->getLatitude();
        $destinationArray[] = $this->getLongitude();

        return implode(', ', $destinationArray);
    }


    /**
     * Get address as string
     *
     * @return string
     */
    public function getAddressAsString(): string
    {
        $destinationArray = [];

        if ($this->getStreet() && $this->getStreetNumber()) {
            $destinationArray[] = $this->getStreet() . ' ' . $this->getStreetNumber();
        }
        if ($this->getStreet() && !$this->getStreetNumber()) {
            $destinationArray[] = $this->getStreet();
        }
        if ($this->getZip() && strlen($this->getZip()) > 2) {
            $destinationArray[] = $this->getZip();
        }
        if ($this->getCity()) {
            $destinationArray[] = $this->getCity();
        }
        if ($this->getCountry()) {
            $destinationArray[] = $this->getCountry();
        }

        return implode(', ', $destinationArray);
    }


    /**
     * Init country to default values
     *
     * @return void
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    protected function initCountry(): void
    {
        if (!empty($this->settings['defaultCountry'])) {
            $this->country = $this->settings['defaultCountry'];

        } else {
            $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
            $extensionConfig = $configReader->get('gadgeto_google');

            if (!empty($extensionConfig['defaultCountry'])) {
                $this->country = $extensionConfig['defaultCountry'];
            }
        }
    }
}
