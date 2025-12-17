<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Controller;

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

use Doctrine\DBAL\Exception;
use Madj2k\GadgetoGoogle\PageTitle\PageTitleProvider;
use Madj2k\GadgetoGoogle\Domain\DTO\Search;
use Madj2k\GadgetoGoogle\Domain\Model\Location;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\SlidingWindowPagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class LocationController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class LocationController extends  AbstractController
{

    /**
     * action list
     *
     * @param int $currentPage // old version
     * @param Search|null $search
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function listAction(int $currentPage = 1, ?Search $search = null): ResponseInterface
    {
        // check if there is something in the session
        if (
            (! $search)
            && ($sessionData = $this->getSessionStorage())
        ){
            $search = $sessionData['search'] ?? null;
        }

        // check identifier - this way the plugin can be used multiple times on the same page
        if (
            (! $search)
            || ($search->getIdentifier() != $this->currentContentObject->data['uid'])
        ){
            $search = GeneralUtility::makeInstance(\Madj2k\GadgetoGoogle\Domain\DTO\Search::class);
            $search->setIdentifier($this->currentContentObject->data['uid']);
        }

        $orderBy = array_merge(
            $this->locationRepository->buildOrderBy($this->settings['orderByFirst'] ?? ''),
            $this->locationRepository->buildOrderBy($this->settings['orderBySecond'] ?? '')
        );

        /** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $locations */
        $locations = $this->locationRepository->findFiltered(
            pidList: $this->currentContentObject->data['pages'] ?? '',
            orderBy: $orderBy,
        );

        // Important: store session for detail view
        $this->setSessionStorage(
            [
                'search' => $search,
                'locations' => $this->locationRepository->getUidListFromObjects($locations)
            ]);

        $maxItemsPerPage = (int) $this->settings['maxResultsPerPage'] ?? 10;
        $maxPages = (int) $this->settings['maxPages'] ?? 3;
        $page = $this->request->hasArgument('currentPage')
            ? $currentPage
            : $search->getPage();

        // if paginationStyle = more: since we only load more, we always start at the first page
        if (
            (isset($this->settings['paginationStyle']))
            && ($this->settings['paginationStyle'] == 'More')
        ) {
            $maxItemsPerPage = $maxItemsPerPage * $page;
            $page = 1;
        }

        /** @var \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator $paginator */
        $paginator = new QueryResultPaginator(
            $locations,
            $page,
            $maxItemsPerPage
        );

        /** @var \TYPO3\CMS\Core\Pagination\SlidingWindowPagination $pagination */
        $pagination = new SlidingWindowPagination(
            $paginator,
            $maxPages,
        );

        $this->view->assignMultiple([
            'search' => $search,
            'locations' => $locations,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'lastPaginatedItem' => $locations[$paginator->getKeyOfLastPaginatedItem()] ?? null,
        ]);

        return $this->htmlResponse();
    }


    /**
     * action teaser
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function teaserAction(): ResponseInterface
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $locations */
        $locations = $this->locationRepository->findFiltered(
            uidList: ($this->settings['locations'] ?? ''),
        );

        $this->setSessionStorage(
            [
                'locations' => $this->locationRepository->getUidListFromObjects($locations)
            ]
        );

        $this->view->assignMultiple([
            'locations' => $locations,
        ]);

        return $this->htmlResponse();
    }


    /**
     * action detail
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Location|null $location
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     **/
    public function detailAction(?Location $location = null): ResponseInterface {

        if (! $location) {
            $this->view->assign('notFound', true);

            $response = $this->htmlResponse();
            return $response->withStatus(404);
        }

        // get navigation-objects if we find one in the session
        $navigationObjects= $this->getNavigationObjects($location);

        // set search from session if set
        $search = null;
        if ($sessionData = $this->getSessionStorage()) {
            $search = $sessionData['search'] ?? null;
        }

        // set page title
        $providerClass = $this->settings['pageTitleProvider'] ?? PageTitleProvider::class;

        /** @var \Madj2k\GadgetoGoogle\PageTitle\PageTitleProviderInterface $provider */
        $provider = GeneralUtility::makeInstance($providerClass);
        $provider->setTitle($location);


        $this->view->assignMultiple(
            [
                'location' => $location,
                'search' => $search,
                'prevLocation' => $navigationObjects['prev'] ?? null,
                'nextLocation' => $navigationObjects['next'] ?? null,
                'firstLocation' => $navigationObjects['first'] ?? null,
                'lastLocation' => $navigationObjects['last'] ?? null,
            ]
        );

        return $this->htmlResponse();

    }


    /**
     * Get the navigation objects for the given location
     *
     * @param Location $location
     * @param string $locationList
     * @return array
     */
    protected function getNavigationObjects (Location $location, string $locationList = ''): array
    {

        if ($sessionData = $this->getSessionStorage()) {
            if (isset($sessionData['locations'])) {
                $locationList = $sessionData['locations'];
            }
        }

        if ($locationList) {

            $languageId = $this->siteLanguage->getLanguageId();
            $uid = (int) $this->currentContentObject->data['uid'];
            $cacheIdentifier = 'navigationobjects_' . $uid . '_' . $languageId . '_' . md5($locationList . $location);
            if (!$navigationObjects = $this->cache->get($cacheIdentifier)) {

                $navigationObjects = $this->locationRepository->findNavigationObjectsByUidList(
                    location: $location,
                    uidList: $locationList
                );

                $this->cache->set(
                    $cacheIdentifier,
                    $navigationObjects,
                    [
                        'gadgetogoogle_navigationobjects', 'gadgetogoogle_navigationobjects_' . $uid . '_' . $languageId
                    ]
                );
            }

            return $navigationObjects;
        }

        return [];
    }
}
