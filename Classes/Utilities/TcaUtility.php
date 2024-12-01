<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Utilities;

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

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TcaUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 *  @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TcaUtility
{

    /**
     * Get the default country from the extConf
     * @return string
     */
    public static function getDefaultCountryByExtConf(): string {

        try {
            $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
            $extensionConfig = $configReader->get('gadgeto_google');
            if (!empty($extensionConfig['defaultCountry'])) {
                return $extensionConfig['defaultCountry'];
            }
        } catch (\Exception $e) {
            // do nothing
        }

        return '';
    }


    /**
     * Is a header in the plugin allowed via the extConf
     *
     * @param string $pluginName
     * @return bool
     */
    public static function isPluginHeaderAllowed(string $pluginName): bool {

        try {
            $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
            $extensionConfig = $configReader->get('gadgeto_google');
            if (!empty($extensionConfig['pluginsWithHeader'])) {
                return in_array($pluginName, GeneralUtility::trimExplode(',', $extensionConfig['pluginsWithHeader'], true));
            }
        } catch (\Exception $e) {
            // do nothing
        }

        return false;
    }


    /**
     * Removed fields from TCA by extConf
     *
     * @param string $fields
     * @return string
     */
    public static function removeFieldsByExtConf(string $fields): string
    {
        try {

            $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
            $extensionConfig = $configReader->get('gadgeto_google');

            if (!empty($extensionConfig['removeFields'])) {
                $fieldsToRemoveArray = GeneralUtility::trimExplode(',', $extensionConfig['removeFields'], true);
                $fieldsArray = GeneralUtility::trimExplode(',', $fields, true);
                foreach ($fieldsToRemoveArray as $fieldToRemove) {
                    if (($key = array_search($fieldToRemove, $fieldsArray)) !== false) {
                        unset($fieldsArray[$key]);
                    }
                }

                $fields = self::trimLinebreaks(implode(', ', $fieldsArray));
            }

        } catch (\Exception $e) {
            // just ignore
        }

        return $fields;
    }


    /**
     * Trim linebreaks from TCA
     * @param $fields
     * @return string
     */
    public static function trimLinebreaks ($fields): string
    {
        // check if a linebreak is at start or end
        $prefix = '--linebreak--';
        if (substr($fields, 0, strlen($prefix)) == $prefix) {
            $fields = trim(trim(substr($fields, strlen($prefix))), ',');
        }
        if (substr($fields, strlen($fields) - strlen($prefix), strlen($fields)) == $prefix) {
            $fields = trim(trim(substr($fields, 0, strlen($prefix) * - 1)), ',');
        }

        return $fields;
    }
}
