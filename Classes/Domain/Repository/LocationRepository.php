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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
     * Finds list of locations
     *
     * @param string $uidListString
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Location[]
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

        /** @var \Madj2k\GadgetoGoogle\Domain\Model\Location $object */
        foreach ($result as $object) {
            $resultSorted[$order[$object->_getProperty('_localizedUid')]] = $object;
        }

        ksort($resultSorted);

        return $resultSorted;
    }


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
     * @see https://tighten.co/blog/a-mysql-distance-function-you-should-know-about
     */
    public function findByDistance(
        float $longitude,
        float $latitude,
        int   $maxDistance = 0,
        int   $limit = 0,
        int   $offset = 0,
    ): array  {

        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ConnectionPool::class);

        /** @var \Doctrine\DBAL\Query\QueryBuilder $conreteQueryBuilder */
        $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());

        $query = $queryBuilder->select('*')
            ->from($this->getTableName())
            ->where('longitude > 0 AND latitude > 0')
            ->orderBy('distance', QueryInterface::ORDER_ASCENDING);

        if ($maxDistance) {
            $query->having('distance < ' . $maxDistance);
        }

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        if ($offset > 0) {
            $query->setFirstResult($offset);
        }

        // add distance via concreteQueryBuilder
        $query->getConcreteQueryBuilder()->addSelect('
                (
                    SELECT ST_Distance_Sphere(
                        point(' . $longitude . ', ' . $latitude . '),
                        point(longitude, latitude)
                    ) * 0.001
                ) AS distance
            ');

        $result = $query->executeQuery()->fetchAllAssociative();

        if ($result) {
            return $this->dataMapper->map(\Madj2k\GadgetoGoogle\Domain\Model\Location::class, $result);
        }

        return [];
    }


    /**
     * Get all categories assigned to records in this repository
     *
     * @param int $languageUid
     * @return array<int, string> uid => title
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findAssignedCategories(int $languageUid = 0): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_category');
        $tableName = $this->getTableName();

        $joinCondition = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('mm.uid_local', $queryBuilder->quoteIdentifier('c.uid')),
            $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter($tableName)),
            $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter('categories'))
        );

        $queryBuilder
            ->select('c.uid', 'c.title')
            ->from('sys_category', 'c')
            ->innerJoin(
                'c',
                'sys_category_record_mm',
                'mm',
                (string)  $joinCondition
            )
            ->innerJoin(
                'mm',
                $tableName,
                't',
                $queryBuilder->expr()->eq('t.uid', $queryBuilder->quoteIdentifier('mm.uid_foreign'))
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'c.sys_language_uid',
                    $queryBuilder->createNamedParameter($languageUid, \Doctrine\DBAL\ParameterType::INTEGER)
                )
            )
            ->groupBy('c.uid', 'c.title')
            ->orderBy('c.sorting', 'ASC')
            ->addOrderBy('c.title', 'ASC');

        return $queryBuilder->executeQuery()->fetchAllAssociative();
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
