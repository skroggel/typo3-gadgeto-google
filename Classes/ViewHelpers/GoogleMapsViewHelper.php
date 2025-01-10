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

use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Resource\Exception\InvalidFileException;
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
        $this->registerArgument('locationCenter', FilterableInterface::class, 'The location that builds the center of the map', false, null);
        $this->registerArgument('mapConfig', 'array', 'The configuration for the Google Map', false, ['zoom' => 12, 'mapTypeControl' => false, 'streetViewControl' => false, 'scrollwheel' => false, 'options' => ['gestureHandling' => 'cooperative']]);
        $this->registerArgument('mapContainerId', 'string', 'The id of the DIV for the map', false, 'tx-gadgetogoogle-map');
        $this->registerArgument('clusterMarkerContainerId', 'string', 'The id of the DIV for the cluster-marker', false, 'tx-gadgetogoogle-map-cluster-marker');
        $this->registerArgument('overlayContainerIdPrefix', 'string', 'The prefix for the DIVs which contain the content for the overlay', false, 'tx-gadgetogoogle-map-overlay');
        $this->registerArgument('filterButtonClass', 'string', 'The class of the buttons for filtering the map', false, 'map-filter-button');
        $this->registerArgument('consentButtonClass', 'string', 'The class of the consent button', false, 'map-consent-button');
        $this->registerArgument('settings', 'array', 'The settings array', false, []);
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

        /** @var \Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface $locationCenter */
        $locationCenter = $this->arguments['locationCenter'];

        /** @var array $mapConfig */
        $mapConfig = $this->arguments['mapConfig'];

        /** @var string $mapContainerId */
        $mapContainerId = $this->arguments['mapContainerId'];

        /** @var string $clusterMarkerContainerId */
        $clusterMarkerContainerId = $this->arguments['clusterMarkerContainerId'];

        /** @var string $filterButtonClass */
        $filterButtonClass = $this->arguments['filterButtonClass'];

        /** @var string $consentButtonClass */
        $consentButtonClass = $this->arguments['consentButtonClass'];

        /** @var array $settings */
        $settings = $this->arguments['settings'];

        $jsFile = PathUtility::getPublicResourceWebPath('EXT:gadgeto_google/Resources/Public/JavaScript/Map.js');

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
            'apiKey' => $googleMapsConfig['apiKeyMap'],
            'mapContainerId' => $mapContainerId,
            'clusterMarkerContainerId' => $clusterMarkerContainerId,
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
            'data' => [],
            'boundaryPositions' => []
        ];

        /*
        if ($locationCenter) {
            $this->buildItemDataArray($locationCenter, $configuration, $settings);
        }*/

        /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $facility */
        foreach ($locations as $location) {
            $this->buildItemDataArray($location, $configuration, $settings);
        }

        return '
            <script type="module">
                import GadgetoGoogleMaps from "' . $jsFile . '";
                let Map = new GadgetoGoogleMaps(' . json_encode($configuration) . ');
            </script>
        ';
    }


    /**
     * @param \Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface $item
     * @param array $configuration
     * @param array $settings
     * @return bool
     */
    protected function buildItemDataArray (FilterableInterface $item, array &$configuration, array $settings = []): bool
    {
        if (
            ($item->getFilterCategory())
            && (
                $item->getLatitude()
                && $item->getLongitude()
            )
        ) {

            /** @var string $overlayContainerIdPrefix */
            $overlayContainerIdPrefix = $this->arguments['overlayContainerIdPrefix'];

            /** @var bool $boundariesByMarkers */
            $boundariesByMarkers = !$this->arguments['locationCenter'];

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
                    'lat' => $item->getLatitude(),
                    'lng' => $item->getLongitude(),
                ]
            ];

            // are the map boundaries to be set by the markers
            if ($boundariesByMarkers) {

                // check for maxSearchDisplayRadius
                if (!empty($settings['maxSearchDisplayRadius'])) {
                    if ($item->getDistance() > (int) $settings['maxSearchDisplayRadius']) {
                        return true;
                    }
                }

                $configuration['boundaryPositions'][] = [
                    'lat' => $item->getLatitude(),
                    'lng' => $item->getLongitude(),
                ];
            }

            return true;
        }

        return false;
    }
}



