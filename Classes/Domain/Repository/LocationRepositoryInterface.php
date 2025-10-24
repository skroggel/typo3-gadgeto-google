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

use Madj2k\GadgetoGoogle\Domain\Model\Category;

/**
 * Class LocationRepositoryInterface
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface LocationRepositoryInterface
{

    /**
     * Finds locations by a comma-separated list of UIDs.
     *
     * @param string $uidList Comma-separated list of UIDs
     * @param string $pidList Optional comma-separated list of PIDs
     * @param int $limit Maximum number of results (0 = unlimited)
     * @param int $offset Result offset (for pagination)
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Location[] Returns an array of Location objects in the given order
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findByUids(
        string $uidList = '',
        string $pidList = '',
        int $limit = 0,
        int $offset = 0
    ): array;


    /**
     * Finds locations based on multiple constraints:
     * - by UID list
     * - by distance (longitude/latitude)
     * - by category
     *
     * @param string $uidList Optional comma-separated list of UIDs
     * @param string $pidList Optional comma-separated list of PIDs
     * @param float $longitude Longitude for distance calculation
     * @param float $latitude Latitude for distance calculation
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Category|null $category Optional category filter
     * @param int $maxDistance Maximum distance in kilometers (0 = unlimited)
     * @param int $limit Maximum number of results (0 = unlimited)
     * @param int $offset Result offset (for pagination)
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Location[] Returns an array of Location objects matching the constraints
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findByConstraints(
        string $uidList = '',
        string $pidList = '',
        float $longitude = 0.0,
        float $latitude = 0.0,
        ?Category $category = null,
        int $maxDistance = 0,
        int $limit = 0,
        int $offset = 0
    ): array;


    /**
     * Retrieves all categories assigned to one or more location records.
     *
     * @param string $uidList Optional comma-separated list of location UIDs to limit the query
     * @param string $pidList Optional comma-separated list of PIDs
     * @param int $languageUid Language UID for category localization
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Category[] Returns an array of Category objects assigned to the locations
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findAssignedCategories(
        string $uidList = '',
        string $pidList = '',
        int $languageUid = 0
    ): array;
}
