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
use Madj2k\GadgetoGoogle\Domain\Repository\FilterCategoryRepository;
use Madj2k\GadgetoGoogle\Service\GeolocationService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class MapController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class MapController extends AbstractController
{

    /**
     * @var \Madj2k\GadgetoGoogle\Domain\Repository\FilterCategoryRepository|null
     */
    protected ?FilterCategoryRepository $filterCategoryRepository;


    /**
     * @param \Madj2k\GadgetoGoogle\Domain\Repository\FilterCategoryRepository $filterCategoryRepository
     * @return void
     */
    public function injectFilterCategoryRepository(FilterCategoryRepository $filterCategoryRepository): void
    {
        $this->filterCategoryRepository = $filterCategoryRepository;
    }


    /**
     * action show
     *
     * @param \Madj2k\GadgetoGoogle\Domain\DTO\Search|null $search
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function showAction(?Search $search = null): ResponseInterface
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

        if ($search && $search->getIsActive()) {

            /** @var \Madj2k\GadgetoGoogle\Service\GeolocationService $geolocationService */
            $geolocationService = GeneralUtility::makeInstance(GeolocationService::class, $this->settings);

            // check if we search by lngLat or address and fetch coordinates accordingly
            $longitude = 0.0;
            $latitude = 0.0;
            if (($search->getLngLatQuery() ||$search->getAddressQuery())) {

                if ($search->getLngLatQuery()) {
                    $geolocationService->setApiCallType(GeolocationService::API_CALL_TYPE_LNGLAT)
                        ->setRawQuery($search->getLngLatQuery());

                } else {
                    $geolocationService->setApiCallType(GeolocationService::API_CALL_TYPE_ADDRESS)
                        ->setRawQuery($search->getAddressQuery());
                }

                if ($geolocationService->fetchData()) {

                    /** @var \Madj2k\GadgetoGoogle\Domain\DTO\Location $currentLocation */
                    $currentLocation = $geolocationService->getLocation();

                    $longitude = $currentLocation->getLongitude();
                    $latitude = $currentLocation->getLatitude();

                    // delete lngLat-value from search - only used once!
                    $search->setLngLatQuery('');

                    // set address from result as feedback
                    $search->setAddressQuery($currentLocation->getAddressAsString());
                }
            }

            $locations = $this->locationRepository->findByConstraints(
                ($this->settings['locations'] ?? ''),
                $longitude,
                $latitude,
                ($search->getCategory() ?? null),
                $search->getRadius() ?? ($this->settings['maxSearchRadius'] ?? 0),
            );

        // normal results
        } else {
            $locations = $this->locationRepository->findByUids($this->settings['locations'] ?? '');
        }

        // Important: store session for detail view
        $this->setSessionStorage(
            [
                'search' => $search,
                'locations' => $this->locationRepository->getUidListFromObjects($locations)
            ]);

        // pagination basics
        $maxItemsPerPage = (intval($this->settings['maxResultsPerPage']) > 0)
            ? intval($this->settings['maxResultsPerPage'])
            : 10;

        // if paginationStyle = more: since we only load more, we always start at the first page
        $page = $search->getPage();
        if ($this->settings['paginationStyle'] == 'More') {
            $maxItemsPerPage = $maxItemsPerPage * $page;
            $page = 1;
        }

        $paginator = new ArrayPaginator($locations, $page, $maxItemsPerPage);
        $pagination = new SimplePagination($paginator);

        /**
         * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $currentContentObject
         */
        $this->view->assignMultiple(
            [
                'search' => $search,
                'locations' => $locations,
                'locationCenter' => $this->locationRepository->findByUid($this->settings['locationCenter']),
                'paginator' => $paginator,
                'pagination' => $pagination,
                'lastPaginatedItem' => $locations[$paginator->getKeyOfLastPaginatedItem()] ?? null,
                'filterCategories' => $this->filterCategoryRepository->findAll(), // deprecated
                'categories' => $this->locationRepository->findAssignedCategories(
                    $this->settings['locations'] ?? '',
                    $this->siteLanguage->getLanguageId()
                ),
            ]
        );

        return $this->htmlResponse();
    }

}
