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

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Madj2k\GadgetoGoogle\Domain\Model\Category;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Class LocationRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class LocationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository implements LocationRepositoryInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper|null
     */
    protected ?DataMapper $dataMapper = null;


    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper
     */
    public function injectDataMapper(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper):void
    {
        $this->dataMapper = $dataMapper;
    }


    /**
     * @var string
     */
    protected string $tableName = '';


    /**
     * Get records based on distance
     *
     * @param float $longitude
     * @param float $latitude
     * @param int $maxDistance
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     * @deprecated use findByConstraints() instead
     */
    public function findByDistance(
        float $longitude,
        float $latitude,
        int   $maxDistance = 0,
        int   $limit = 0,
        int   $offset = 0,
    ): array
    {
        return $this->findByConstraints(
            '',
            $longitude,
            $latitude,
            null,
            $maxDistance,
            $limit,
            $offset
        );
    }


    /**
     * Finds locations by a comma-separated list of UIDs.
     *
     * @param string $uidList Comma-separated list of UIDs
     * @param int $limit Maximum number of results (0 = unlimited)
     * @param int $offset Result offset (for pagination)
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Location[] Returns an array of Location objects in the given order
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findByUids(
        string $uidList = '',
        int   $limit = 0,
        int   $offset = 0): array
    {
        return $this->findByConstraints($uidList,
            0,
            0,
            null,
            0,
            $limit,
            $offset);
    }


    /**
     * Finds locations based on multiple constraints:
     * - by UID list
     * - by distance (longitude/latitude)
     * - by category
     *
     * @param string $uidList Optional comma-separated list of UIDs
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
        float $longitude = 0.0,
        float $latitude = 0.0,
        Category $category = null,
        int $maxDistance = 0,
        int $limit = 0,
        int $offset = 0,
    ): array  {

        $uidListArray = [];
        if ($uidList) {
            $uidListArray = GeneralUtility::trimExplode(',', $uidList);
        }

        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ConnectionPool::class);

        /** @var \Doctrine\DBAL\Query\QueryBuilder $conreteQueryBuilder */
        $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());

        $query = $queryBuilder->select('l.*')
            ->from($this->getTableName(), 'l');

        // search by distance
        if ($longitude && $latitude)  {
            $this->addDistanceConstraints($query, $longitude, $latitude, $maxDistance);
        } else {
            $query->orderBy('label', QueryInterface::ORDER_ASCENDING);
        }

        // search by category
        if ($category) {
            $this->addCategoryConstraint($query, $category);
        }

        // filter by uidList
        if ($uidList) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'l.uid',
                    $queryBuilder->createNamedParameter($uidListArray, ArrayParameterType::INTEGER)
                )
            );
        }

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        if ($offset > 0) {
            $query->setFirstResult($offset);
        }

        $result = $this->dataMapperForLocations($query->executeQuery()->fetchAllAssociative());
        if ($result) {

            // if the results are to filtered by uidList AND we do not have a distance-search,
            // then we sort the result by the given uidList
            if (!($longitude && $latitude) && $uidListArray) {

                // now sort by the given order.
                $order = array_flip($uidListArray);
                $resultSorted = [];

                /** @var \Madj2k\GadgetoGoogle\Domain\Model\Location $object */
                foreach ($result as $object) {
                    $resultSorted[$order[$object->_getProperty('_localizedUid')]] = $object;
                }

                ksort($resultSorted);
                $result = $resultSorted;
            }

            return $result;

        }
        return [];
    }


    /**
     * Add constraints to fetch records based on distance
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param float $longitude
     * @param float $latitude
     * @param int $maxDistance
     * @return void
     * @see https://tighten.co/blog/a-mysql-distance-function-you-should-know-about
     */
    protected function addDistanceConstraints(
        QueryBuilder $query,
        float $longitude,
        float $latitude,
        int $maxDistance
    ): void  {

        $query->andWhere('l.longitude > 0 AND l.latitude > 0')
            ->orderBy('distance', QueryInterface::ORDER_ASCENDING);

        if ($maxDistance) {
            $query->having('distance < ' . $maxDistance);
        }

        // add distance via concreteQueryBuilder
        $query->getConcreteQueryBuilder()->addSelect('
                (
                    SELECT ST_Distance_Sphere(
                        point(' . $longitude . ', ' . $latitude . '),
                        point(l.longitude, l.latitude)
                    ) * 0.001
                ) AS distance
            ');
    }


    /**
     * Add constraints to fetch records based on category
     *
     * QueryBuilder $query
     * @param QueryBuilder $query
     * @param Category $category
     * @param int $languageUid
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    protected function addCategoryConstraint(QueryBuilder $query, Category $category, int $languageUid = 0): void
    {
        $joinCondition = $query->expr()->and(
            $query->expr()->eq('cat_mm.uid_foreign', $query->quoteIdentifier('l.uid')),
            $query->expr()->eq('cat_mm.tablenames', $query->createNamedParameter($this->getTableName())),
            $query->expr()->eq('cat_mm.fieldname', $query->createNamedParameter('categories')),
            $query->expr()->eq('cat_mm.uid_local', $query->createNamedParameter($category->getUid()))
        );

        $query->innerJoin(
                'l',
                'sys_category_record_mm',
                'cat_mm',
                (string) $joinCondition
            )
            ->innerJoin('cat_mm',
                'sys_category',
                'c',
                $query->expr()->eq(
                    'c.uid',
                    $query->quoteIdentifier('cat_mm.uid_local')
                )
            )
            ->andWhere(
                $query->expr()->eq(
                    'l.sys_language_uid',
                    $query->createNamedParameter($languageUid, ParameterType::INTEGER))
            );
    }


    /**
     * Retrieves all categories assigned to one or more location records.
     *
     * @param string $uidList Optional comma-separated list of location UIDs to limit the query
     * @param int $languageUid Language UID for category localization
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Category[] Returns an array of Category objects assigned to the locations
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findAssignedCategories(string $uidList = '', int $languageUid = 0): array
    {
        $uidListArray = [];
        if ($uidList) {
            $uidListArray = GeneralUtility::trimExplode(',', $uidList);
        }

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_category');

        $joinConditionLocal = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('cat_mm.uid_local', $queryBuilder->quoteIdentifier('c.uid')),
            $queryBuilder->expr()->eq('cat_mm.tablenames', $queryBuilder->createNamedParameter($this->getTableName())),
            $queryBuilder->expr()->eq('cat_mm.fieldname', $queryBuilder->createNamedParameter('categories'))
        );

        $joinConditionForeign = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('cat_mm.uid_foreign', $queryBuilder->quoteIdentifier('l.uid')),
            $queryBuilder->expr()->eq('cat_mm.tablenames', $queryBuilder->createNamedParameter($this->getTableName())),
            $queryBuilder->expr()->eq('cat_mm.fieldname', $queryBuilder->createNamedParameter('categories')),
        );

        $query = $queryBuilder
            ->select('c.uid', 'c.title')
            ->from('sys_category', 'c')
            ->innerJoin(
                'c',
                'sys_category_record_mm',
                'cat_mm',
                (string)  $joinConditionLocal
            )
            ->innerJoin(
                'cat_mm',
                $this->getTableName(),
                't',
                $queryBuilder->expr()->eq(
                    't.uid',
                    $queryBuilder->quoteIdentifier('cat_mm.uid_foreign'
                    )
                )
            )
            ->innerJoin(
                'cat_mm',
                $this->getTableName(),
                'l',
                (string) $joinConditionForeign
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'c.sys_language_uid',
                    $queryBuilder->createNamedParameter(
                        $languageUid,
                        \Doctrine\DBAL\ParameterType::INTEGER
                    )
                )
            )
            ->groupBy('c.uid', 'c.title')
            ->orderBy('c.sorting', 'ASC')
            ->addOrderBy('c.title', 'ASC');

        // filter by uidList if given!
        if ($uidListArray) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'l.uid',
                    $queryBuilder->createNamedParameter($uidListArray, ArrayParameterType::INTEGER)
                )
            );
        }

        return $this->dataMapperForCategories($query->executeQuery()->fetchAllAssociative());
    }


    /**
     * Data-mapper for categories
     *
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Category[]
     */
    public function dataMapperForCategories(array $queryResult = []): array
    {
        if ($queryResult) {
            return $this->dataMapper->map(\Madj2k\GadgetoGoogle\Domain\Model\Category::class, $queryResult);
        }
        return $queryResult;
    }


    /**
     * Data-mapper for locations
     *
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Location[]
     */
    public function dataMapperForLocations(array $queryResult = []): array
    {
        if ($queryResult) {
            return $this->dataMapper->map(\Madj2k\GadgetoGoogle\Domain\Model\Location::class, $queryResult);
        }
        return $queryResult;
    }


    /**
     * Return the current table name
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function getTableName(): string
    {
        if (!$this->tableName) {

            $className = $this->createQuery()->getType();

            /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper */
            $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
            $this->tableName = $dataMapper->getDataMap($className)->getTableName();
        }

        return $this->tableName;
    }

}
