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
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * Allow mapping of properties to DTO even if no object is submitted (e.g. when using GET)
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeShowAction(): void
    {
        if ($this->arguments->hasArgument('search')) {
            $propertyMappingConfiguration = $this->arguments->getArgument('search')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowAllProperties();
        }
    }


    /**
     * action show
     *
     * @param \Madj2k\GadgetoGoogle\Domain\DTO\Search|null $search
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function showAction(?Search $search = null): ResponseInterface
    {

        /** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $filterCategories */
        $filterCategories = $this->filterCategoryRepository->findAll();

        /** @var \Madj2k\GadgetoGoogle\Domain\Model\Location $locationCenter */
        $locationCenter = $this->locationRepository->findByUid($this->settings['locationCenter']);

        $locations = [];
        if ($search && $search->getIsActive()) {

            /** @var \Madj2k\GadgetoGoogle\Service\GeolocationService $geolocationService */
            $geolocationService = GeneralUtility::makeInstance(GeolocationService::class, $this->settings);

            // check if we search by lngLat or address
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
                $locations = $this->locationRepository->findByDistance(
                    $currentLocation->getLongitude(),
                    $currentLocation->getLatitude(),
                    (int) $this->settings['maxSearchRadius'] ?: 0
                );

                // delete lngLat-value from search - only used once!
                $search->setLngLatQuery('');

                // set address from result as feedback
                $search->setAddressQuery($currentLocation->getAddressAsString());
            }

        // normal results
        } else {
            $search = GeneralUtility::makeInstance(\Madj2k\GadgetoGoogle\Domain\DTO\Search::class);

            /** @var array $allLocations */
            $locations = $this->locationRepository->findByUids($this->settings['locations']);
        }

        /**
         * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $currentContentObject
         */
        $this->view->assignMultiple(
            [
                'search' => $search,
                'locations' => $locations,
                'locationCenter' => $locationCenter,
                'filterCategories' => $filterCategories,
            ]
        );

        return $this->htmlResponse();
    }

}
