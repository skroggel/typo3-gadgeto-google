<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Domain\Repository;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class LocationRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class LocationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Finds list of locations
     *
     * @param string $uidListString
     * @return \Madj2k\GadgetoGoogle\Domain\Location[]
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByUids(string $uidListString = ''): array
    {
        // generate list as array
        $uidList = GeneralUtility::trimExplode(',', $uidListString);

        $query = $this->createQuery();
        $result =  $query
            ->matching(
                $query->in('uid', $uidList)
            )
            ->execute();

        // now sort by the given order.
        $order = array_flip($uidList);
        $resultSorted = [];

        /** @var \Madj2k\GadgetoGoogle\Domain\Location $object */
        foreach ($result as $object) {
            $resultSorted[$order[$object->_getProperty('_localizedUid')]] = $object;
        }

        ksort($resultSorted);

        return $resultSorted;
    }

}
