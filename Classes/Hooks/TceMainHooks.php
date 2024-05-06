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

use Madj2k\GadgetoGoogle\Service\Geolocation;
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
            if ($table == 'tx_gadgetogoogle_domain_model_location') {

                /** @var \Madj2k\GadgetoGoogle\Service\Geolocation $geolocation */
                $geolocation = GeneralUtility::makeInstance(Geolocation::class);
                if ($geolocation->insertDataFromRecord($table, $id, $fieldArray)) {
                    $geolocation->setApiCallType(Geolocation::API_CALL_TYPE_ADDRESS)
                        ->fetchData();
                    $fieldArray['longitude'] = $geolocation->getLongitude();
                    $fieldArray['latitude'] = $geolocation->getLatitude();
                }
            }
        } catch (\Exception $e) {
            // don't bother anyone
        }
    }
}
