<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\ViewHelpers;

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

use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Resource\Exception\InvalidFileException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface;

/**
 * Class GoogleMapsViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GoogleMapsViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;


    /**
     * @var \TYPO3\CMS\Core\Registry|null
     */
    protected ?Registry $registry = null;


    /**
     * @param \TYPO3\CMS\Core\Registry $registry
     * @return void
     */
    public function injectRegistry(Registry $registry):void
    {
        $this->registry = $registry;
    }


    /**
     * Initialize arguments
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('locations', QueryResultInterface::class, 'The locations to display', true);
        $this->registerArgument('locationCenter', FilterableInterface::class, 'The location that builds the center of the map');
        $this->registerArgument('mapConfig', 'array', 'The configuration for the Google Map', false, ['zoom' => 12, 'mapTypeControl' => false, 'streetViewControl' => false, 'scrollwheel' => false, 'options' => ['gestureHandling' => 'cooperative']]);
        $this->registerArgument('mapContainerId', 'string', 'The id of the DIV for the map', false, 'tx-madj2k-map');
        $this->registerArgument('filterButtonClass', 'string', 'The class of the buttons for filtering the map', false, 'map-filter-button');
        $this->registerArgument('consentButtonClass', 'string', 'The class of the consent button', false, 'map-consent-button');
        $this->registerArgument('overlayContainerIdPrefix', 'string', 'The prefix for the DIVs which contain the content for  the overlay', false, 'tx-madj2k-map-overlay');
    }


    /**
     * Render the configuration for the map and the map itself.
     *
     * @throws \UnexpectedValueException
     * @throws Exception
     * @throws InvalidFileException
     */
    public function render(): string
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface<\Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface> $locations */
        $locations = $this->arguments['locations'];

        /**
         * @var \Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface $locationCenter
         */
        $locationCenter = $this->arguments['locationCenter'];
        $mapConfig = $this->arguments['mapConfig'];
        $mapContainerId = $this->arguments['mapContainerId'];
        $filterButtonClass = $this->arguments['filterButtonClass'];
        $consentButtonClass = $this->arguments['consentButtonClass'];
        $jsFile = PathUtility::getPublicResourceWebPath('EXT:gadgeto_google/Resources/Public/JavaScript/Map.mjs');

        $centerLongitude = $locations[0]->getLongitude();
        $centerLatitude = $locations[0]->getLatitude();
        if (
            ($locationCenter)
            && ($locationCenter->getLatitude())
            && ($locationCenter->getLongitude())
        ) {
            $centerLatitude = $locationCenter->getLatitude();
            $centerLongitude = $locationCenter->getLongitude();
        }

        $googleMapsConfig = $this->registry->get(
            'gadgeto_google',
            'googleMapsConfig',
        );

        $configuration = [
            'apiKey' => $googleMapsConfig['apiKey'],
            'mapContainerId' => $mapContainerId,
            'filterButtonClass' => $filterButtonClass,
            'consentButtonClass' => $consentButtonClass,
            'mapConfig' => array_merge(
                $mapConfig, [
                    'mapId' => $googleMapsConfig['mapId'],
                    'center' => [
                        'lat' => (float)$centerLatitude,
                        'lng' => (float) $centerLongitude,
                    ],
                ]
            ),
            'data' => []
        ];

        if ($locationCenter) {
            $this->buildItemDataArray($locationCenter, $configuration);
        }

        /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $facility */
        foreach ($locations as $location) {
            $this->buildItemDataArray($location, $configuration);
        }

        $javaScript = '
            <script type="module">
                import Madj2kGoogleMaps from "' . $jsFile . '";
                let Map = new Madj2kGoogleMaps(' . json_encode($configuration) . ');
            </script>
        ';

        return $javaScript . '';
    }


    /**
     * @param \Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface $item
     * @param array $configuration
     * @return bool
     */
    protected function buildItemDataArray (FilterableInterface $item, array &$configuration): bool
    {
        if (
            ($item->getFilterCategory())
            && (
                $item->getLatitude()
                && $item->getLongitude()
            )
        ) {

            $overlayContainerIdPrefix = $this->arguments['overlayContainerIdPrefix'];

            // build string of selected searchTypes
            $categories = [];
            /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $type */
            foreach ($item->getFilterCategory() as $type) {
                $categories[] = $type->getUid();
            }

            if (! is_array($configuration['data'])) {
                $configuration['data'] = [];
            }

            $configuration['data'][] = [
                'id' => $item->getUid(),
                'label' => $item->getLabel(),
                'categories' => implode(',', $categories),
                'overlayContainerId' => $overlayContainerIdPrefix . '-' . md5(spl_object_hash($item)),
                'position' => [
                    'lat' => (float)$item->getLatitude(),
                    'lng' => (float)$item->getLongitude(),
                ]
            ];

            return true;
        }

        return false;
    }
}



