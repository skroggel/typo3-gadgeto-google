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

use Madj2k\GadgetoGoogle\Domain\Repository\FilterCategoryRepository;
use Madj2k\GadgetoGoogle\Domain\Repository\LocationRepository;
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
final class MapController extends  \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \Madj2k\GadgetoGoogle\Domain\Repository\LocationRepository|null
     */
    protected ?LocationRepository $locationRepository;


    /**
     * @var \Madj2k\GadgetoGoogle\Domain\Repository\FilterCategoryRepository|null
     */
    protected ?FilterCategoryRepository $filterCategoryRepository;


    /**
	 * @param \Madj2k\GadgetoGoogle\Domain\Repository\LocationRepository $locationRepository
	 * @return void
	 */
	public function injectLocationRepository(LocationRepository $locationRepository):void
	{
		$this->locationRepository = $locationRepository;
	}


    /**
     * @param \Madj2k\GadgetoGoogle\Domain\RepositoryFilterCategoryRepository $filterCategoryRepository
     * @return void
     */
    public function injectFilterCategoryRepository(FilterCategoryRepository $filterCategoryRepository):void
    {
        $this->filterCategoryRepository = $filterCategoryRepository;
    }


    /**
     * action show
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function showAction(): ResponseInterface
    {
        $locatations = $this->locationRepository->findByUid($this->settings['locations']);
        $locatationCenter = $this->locationRepository->findByUid($this->settings['locationCenter']);
        $filterCategories = $this->filterCategoryRepository->findAll();

        /**
         * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $currentContentObject
         */
        $currentContentObject = $this->request->getAttribute('currentContentObject');
        $this->view->assignMultiple(
            [
                'locations' => $locations,
                'locationCenter' => $locationCenter,
                'filterCategories' => $filterCategories,
                'contentUid' => $currentContentObject->data['uid']
            ]
        );

        return $this->htmlResponse();
    }
}
