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


use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\SlidingWindowPagination;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;

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
     * @param int $currentPage
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(int $currentPage = 1): ResponseInterface
    {

        /** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $locations */
        $locations = $this->locationRepository->findAll();

        $maxItemsPerPage = (int) $this->settings['maxResultsPerPage'] ?? 10;
        $maxPages = (int) $this->settings['maxPages'] ?? 3;

        /** @var \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator $paginator */
        $paginator = new QueryResultPaginator(
            $locations,
            $currentPage,
            $maxItemsPerPage
        );

        /** @var \TYPO3\CMS\Core\Pagination\SlidingWindowPagination $pagination */
        $pagination = new SlidingWindowPagination(
            $paginator,
            $maxPages,
        );

        $this->view->assignMultiple([
            'paginator' => $paginator,
            'pagination' => $pagination,
        ]);

        /**
         * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $currentContentObject
         */
        $this->view->assignMultiple(
            [
                'locations' => $locations,
            ]
        );

        return $this->htmlResponse();
    }
}
