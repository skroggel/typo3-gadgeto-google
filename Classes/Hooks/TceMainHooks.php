<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Hooks;
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

use Madj2k\GadgetoGoogle\Service\GeolocationService;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TceMainHooks
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TceMainHooks
{

    /**
     * Fetches GeoData
     *
     * @param $status
     * @param $table
     * @param $id
     * @param $fieldArray
     * @param $reference
     * @return void
     */
    function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$reference): void
    {
        try {

            $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
            $extensionConfig = $configReader->get('gadgeto_google');

            $apiHookTables = [];
            if (! empty($extensionConfig['apiHookTables'])) {
                $apiHookTables = GeneralUtility::trimExplode(',', $extensionConfig['apiHookTables'], true);
            }

            if (in_array($table, $apiHookTables)) {

                /** @var \Madj2k\GadgetoGoogle\Service\GeolocationService $geolocationService*/
                $geolocationService = GeneralUtility::makeInstance(GeolocationService::class);
                if ($geolocationService->insertDataFromRecord($table, $id, $fieldArray)) {
                    $geolocationService->setApiCallType(GeolocationService::API_CALL_TYPE_ADDRESS)
                        ->fetchData();
                    $fieldArray['longitude'] = $geolocationService->getLocation()->getLongitude();
                    $fieldArray['latitude'] = $geolocationService->getLocation()->getLatitude();
                }
            }
        } catch (\Exception $e) {
            // don't bother anyone
        }
    }
}
