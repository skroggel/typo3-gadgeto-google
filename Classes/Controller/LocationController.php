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
        }

        /** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $locations */
        $locations = $this->locationRepository->findByConstraints(
            '',
            $this->currentContentObject->data['pages'] ?? '',
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
        if ($this->settings['paginationStyle'] == 'More') {
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
        $locations = $this->locationRepository->findByConstraints($this->settings['locations'] ?? '');

        $this->view->assignMultiple([
            'locations' => $locations,
        ]);

        return $this->htmlResponse();
    }


    /**
     * action detail
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Location|null $location
     * @return ResponseInterface
     */
    public function detailAction(?Location $location = null): ResponseInterface {

        if (! $location) {
            $this->view->assign('notFound', true);

            $response = $this->htmlResponse();
            return $response->withStatus(404);
        }

        // get previous and next location if we find one in the session
        $prevNextLocation = [];
        $search = null;
        if ($sessionData = $this->getSessionStorage()) {
            $search = $sessionData['search'] ?? null;
            if (isset($sessionData['locations'])) {
                $prevNextLocation = $this->locationRepository->findPrevAndNextObjectsByUidList(
                    $location,
                    $sessionData['locations']
                );
            }
        }

        $this->view->assignMultiple(
            [
                'location' => $location,
                'search' => $search,
                'prevLocation' => $prevNextLocation['prev'] ?? null,
                'nextLocation' => $prevNextLocation['next'] ?? null,
            ]
        );

        return $this->htmlResponse();

    }
}
