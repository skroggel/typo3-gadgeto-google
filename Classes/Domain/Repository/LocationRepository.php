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
use Madj2k\GadgetoGoogle\Domain\DTO\Search;
use Madj2k\GadgetoGoogle\Domain\DTO\Location as LocationDto;
use Madj2k\GadgetoGoogle\Domain\Model\Category;
use Madj2k\GadgetoGoogle\Domain\Model\Location;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
     * @deprecated use findFiltered() instead
     */
    public function findByDistance(
        float $longitude,
        float $latitude,
        int   $maxDistance = 0,
        int   $limit = 0,
        int   $offset = 0,
    ): array
    {

        /** @var \Madj2k\GadgetoGoogle\Domain\DTO\Location $location */
        $location = new LocationDto();
        $location->setLongitude($longitude);
        $location->setLatitude($latitude);

        $settings = ['maxSearchRadius' => $maxDistance];

        return $this->findFiltered(
            location: $location,
            settings: $settings,
            limit: $limit,
            offset: $offset
        );
    }


    /**
     * Finds locations by a comma-separated list of UIDs.
     *
     * @param string $uidList Comma-separated list of UIDs
     * @param string $pidList Optional comma-separated list of PIDs
     * @param int $limit Maximum number of results (0 = unlimited)
     * @param int $offset Result offset (for pagination)
     * @param array $orderBy Order-array
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Location[] Returns an array of Location objects in the given order
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findByUids(
        string $uidList = '',
        string $pidList = '',
        int   $limit = 0,
        int   $offset = 0,
        array $orderBy = ['label' => QueryInterface::ORDER_ASCENDING]
    ): array {

        return $this->findFiltered(
            uidList: $uidList,
            pidList: $pidList,
            limit: $limit,
            offset: $offset,
            orderBy: $orderBy
        );
    }


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
     * @deprecated use findFiltered() instead
     */
    public function findByConstraints(
        string $uidList = '',
        string $pidList = '',
        float $longitude = 0.0,
        float $latitude = 0.0,
        Category $category = null,
        int $maxDistance = 0,
        int $limit = 0,
        int $offset = 0,
    ): array  {

        /** @var \Madj2k\GadgetoGoogle\Domain\DTO\Location $location */
        $location = new LocationDto();
        $location->setLongitude($longitude);
        $location->setLatitude($latitude);

        /** @var \Madj2k\GadgetoGoogle\Domain\DTO\Search $search */
        $search = new Search();
        $search->setCategory($category);
        $search->setRadius($maxDistance);

        return $this->findFiltered(
            uidList: $uidList,
            pidList: $pidList,
            search: $search,
            location: $location,
            limit: $limit,
            offset: $offset
        );
    }


    /**
     * Finds locations based on multiple constraints:
     * - by UID list
     * - by distance (longitude/latitude)
     * - by category
     *
     * @param string $uidList Optional comma-separated list of UIDs
     * @param string $pidList Optional comma-separated list of PIDs
     * @param \Madj2k\GadgetoGoogle\Domain\DTO\Search|null $search Search-object
     * @param \Madj2k\GadgetoGoogle\Domain\DTO\Location|null $location Location-object from API
     * @param array $settings settings-array
     * @param int $limit Maximum number of results (0 = unlimited)
     * @param int $offset Result offset (for pagination)
     * @param array $orderBy Order-array
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Location[] Returns an array of Location objects matching the constraints
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findFiltered(
        string $uidList = '',
        string $pidList = '',
        ?Search $search = null,
        ?LocationDto $location = null,
        array $settings = [],
        int $limit = 0,
        int $offset = 0,
        array $orderBy = ['label' => QueryInterface::ORDER_ASCENDING]
    ): array  {

        $languageField = $GLOBALS['TCA'][$this->getTableName()]['ctrl']['languageField'] ?? '';
        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;

        $uidListArray = [];
        if ($uidList) {
            $uidListArray = GeneralUtility::trimExplode(',', $uidList);
        }

        $pidListArray = [];
        if ($pidList) {
            $pidListArray = GeneralUtility::trimExplode(',', $pidList);
        }

        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ConnectionPool::class);

        /** @var \Doctrine\DBAL\Query\QueryBuilder $conreteQueryBuilder */
        $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());

        $query = $queryBuilder->select('l.*')
            ->from($this->getTableName(), 'l');

        if ($languageField) {
            $query->where('l.' . $languageField . ' = ' . $queryBuilder->createNamedParameter(
                $languageUid, ParameterType::INTEGER
                )
            );
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

        // filter by pidList
        if ($pidList) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'l.pid',
                    $queryBuilder->createNamedParameter($pidListArray, ArrayParameterType::INTEGER)
                )
            );
        }

        // search by category
        if ($search && $search->getCategory()) {
            $this->addCategoryConstraint(
                query: $query,
                category: $search->getCategory(),
            );
        }

        // search by distance
        if ($location && $location->getLongitude() && $location->getLatitude())  {
            $maxDistance = (int) (($search && $search->getRadius()) ? $search->getRadius() : ($settings['maxSearchRadius'] ?? 0));

            $this->addDistanceConstraints(
                query: $query,
                longitude: $location->getLongitude(),
                latitude: $location->getLatitude(),
                maxDistance: $maxDistance
            );

        } else {
            foreach($orderBy as $field => $order) {
                $query->addOrderBy('l.' . $field, $order);
            }
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
            if ((!$location || !$location->getLongitude() || !$location->getLatitude()) && $uidListArray) {

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
     * @param QueryBuilder $query
     * @param Category $category
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    protected function addCategoryConstraint(QueryBuilder $query, Category $category): void
    {
        // at this point TYPO3 has already mapped the translated category to the original record!
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
            );
    }


    /**
     * Retrieves all categories assigned to one or more location records.
     *
     * @param string $uidList Optional comma-separated list of location UIDs to limit the query
     * @param string $pidList Optional comma-separated list of PIDs
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Category[] Returns an array of Category objects assigned to the locations
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findAssignedCategories(
        string $uidList = '',
        string $pidList = '',
    ): array {

        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;
        $uidListArray = [];
        if ($uidList) {
            $uidListArray = GeneralUtility::trimExplode(',', $uidList);
        }

        $pidListArray = [];
        if ($pidList) {
            $pidListArray = GeneralUtility::trimExplode(',', $pidList);
        }

        $languageField = $GLOBALS['TCA']['sys_category']['ctrl']['languageField'] ?? '';
        $uidField = 'uid';
        if ($languageUid > 0) {
            $uidField = $GLOBALS['TCA']['sys_category']['ctrl']['transOrigPointerField'] ?? 'uid';
        }

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_category');

        $joinConditionLocal = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('cat_mm.uid_local', $queryBuilder->quoteIdentifier('c.' . $uidField)),
            $queryBuilder->expr()->eq('cat_mm.tablenames', $queryBuilder->createNamedParameter($this->getTableName())),
            $queryBuilder->expr()->eq('cat_mm.fieldname', $queryBuilder->createNamedParameter('categories'))
        );

        $joinConditionForeign = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('cat_mm.uid_foreign', $queryBuilder->quoteIdentifier('l.uid')),
            $queryBuilder->expr()->eq('cat_mm.tablenames', $queryBuilder->createNamedParameter($this->getTableName())),
            $queryBuilder->expr()->eq('cat_mm.fieldname', $queryBuilder->createNamedParameter('categories')),
        );

        // map localized title to original uid if translated
        $queryBuilder
            ->select('c.' . $uidField . ' as uid', 'c.title')
            ->from('sys_category', 'c')
            ->innerJoin(
                'c',
                'sys_category_record_mm',
                'cat_mm',
                (string)  $joinConditionLocal
            )
            // make sure relations of hidden elements are not included
            ->innerJoin(
                'cat_mm',
                $this->getTableName(),
                't',
                $queryBuilder->expr()->eq('t.uid', $queryBuilder->quoteIdentifier('cat_mm.uid_foreign'))
            )
            ->innerJoin(
                'cat_mm',
                $this->getTableName(),
                'l',
                (string) $joinConditionForeign
            )
            ->groupBy('c.uid', 'c.title')
            ->orderBy('c.sorting', 'ASC')
            ->addOrderBy('c.title', 'ASC');


        if ($languageField) {
            $queryBuilder->where(
                $queryBuilder->expr()->eq(
                    'c.' . $languageField,
                    $queryBuilder->createNamedParameter(
                        $languageUid,
                        \Doctrine\DBAL\ParameterType::INTEGER
                    )
                )
            );
        }

        // filter by uidList if given!
        if ($uidListArray) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'l.uid',
                    $queryBuilder->createNamedParameter($uidListArray, ArrayParameterType::INTEGER)
                )
            );
        }

        // filter by pidList if given!
        if ($pidList) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'l.pid',
                    $queryBuilder->createNamedParameter($pidListArray, ArrayParameterType::INTEGER)
                )
            );
        }

        return $this->dataMapperForCategories($queryBuilder->executeQuery()->fetchAllAssociative());
    }


    /**
     * Extracts a list of UIDs from the given objects and returns them as a comma-separated string.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array $objects Collection of objects to extract UIDs from. Each object must have a `getUid` method.
     * @return string A comma-separated string of UIDs.
     */
    public function getUidListFromObjects(QueryResultInterface|array $objects): string
    {
        /** @var int[] $uids */
        $uids = [];

        foreach ($objects as $object) {
            if (method_exists($object, 'getUid')) {
                $uids[] = (int)$object->getUid();
            }
        }

        return implode(',', $uids);
    }


    /**
     * Finds the previous and next objects relative to a provided location object based on a UID list.
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Location $location The reference location object.
     * @param string $uidList A comma-separated list of UIDs representing the sequence to search within.
     * @return array An associative array with keys 'prev' and 'next', containing the previous and next objects respectively.
     *               If there is no previous or next object, the respective value will be null.
     * @deprecated Use $this->findNavigationObjectsByUidList() instead
     */
    public function findPrevAndNextObjectsByUidList(Location $location, string $uidList = ''): array
    {
        return $this->findNavigationObjectsByUidList($location, $uidList);
    }


    /**
     * Finds the previous and next objects relative to a provided location object based on a UID list.
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Location $location The reference location object.
     * @param string $uidList A comma-separated list of UIDs representing the sequence to search within.
     * @return array An associative array with keys 'prev' and 'next', containing the previous and next objects respectively.
     *               If there is no previous or next object, the respective value will be null.
     */
    public function findNavigationObjectsByUidList(Location $location, string $uidList = ''): array
    {
        /** @var int[] $uids */
        $prev = $next = $first = $last = null;
        if ($uidList) {
            $uids = array_map('intval', explode(',', $uidList));
            $index = array_search($location->getUid(), $uids, true);

            if ($index === false) {
                return ['prev' => null, 'next' => null, 'first' => null, 'last' => null];
            }

            $prevUid = $uids[$index - 1] ?? null;
            $nextUid = $uids[$index + 1] ?? null;
            $firstUid = $uids[0] ?? null;
            $lastUid = $uids[count($uids) - 1] ?? null;

            /** @var \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface|null $prev */
            $prev = $prevUid !== null ? $this->findByUid($prevUid) : null;

            /** @var \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface|null $next */
            $next = $nextUid !== null ? $this->findByUid($nextUid) : null;

            /** @var \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface|null $first */
            $first = $firstUid !== null ? $this->findByUid($firstUid) : null;

            /** @var \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface|null $last */
            $last = $lastUid !== null ? $this->findByUid($lastUid) : null;
        }

        return [
            'prev' => $prev,
            'next' => $next,
            'first' => $first,
            'last' => $last,
        ];

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
     * Build an order array from a given string
     *
     * @param string $ordering
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function buildOrderBy (string $ordering): array {

        $split = GeneralUtility::trimExplode(';', $ordering);
        $field = strtolower($split[0] ?? 'label');
        $direction = strtoupper($split[1] ?? 'ASC');

        if ($direction == 'DESC') {
            $direction = QueryInterface::ORDER_DESCENDING;
        } else {
            $direction = QueryInterface::ORDER_ASCENDING;
        }

        if (isset($GLOBALS['TCA'][$this->getTableName()]['columns'][$field])) {
            return [$field => $direction];
        }

        return [];
    }


    /**
     * Return the current table name
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    protected function getTableName(): string
    {
        if (!$this->tableName) {

            $className = $this->createQuery()->getType();

            /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper */
            $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
            $this->tableName = $dataMapper->getDataMap($className)->getTableName();
        }

        return $this->tableName;
    }


    /**
     * Return the current SiteLanguage-object
     *
     * @return \TYPO3\CMS\Core\Site\Entity\SiteLanguage|null
     */
    protected function getSiteLanguage(): ?SiteLanguage
    {
        if ($request = $this->getRequest()) {
            return $request->getAttribute('language');
        }

        return null;
    }


    /**
     * Get request object
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
