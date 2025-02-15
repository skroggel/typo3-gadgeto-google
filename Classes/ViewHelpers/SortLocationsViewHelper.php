<?php
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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class SortLocationsViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SortLocationsViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('items', 'array', 'The locations to sort.');
        $this->registerArgument('sortField', 'string', 'The sort field if no sorting value is set.', false, 'label');
        $this->registerArgument('sortDirection', 'string', 'The sort direction.', false, 'asc');
    }


    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return array
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ):  array {

        /** @var array $items  */
        $items = $arguments['items'];

        /** @var string $sortField */
        $sortField = $arguments['sortField'];

        /** @var string $sortDirection */
        $sortDirection = $arguments['sortDirection'];

        $result = [];
        if ($items) {

            $resultKey = [];
            $resultSorting = [];
            $sortingUsed = false;

            /** @var \Madj2k\GadgetoGoogle\Domain\Model\Location $location */
            foreach ($items as $location) {

                if ($location  instanceof \Madj2k\GadgetoGoogle\Domain\Model\Location) {

                    $getter = 'get' . ucfirst($sortField);
                    if (method_exists($location, $getter)) {
                        $resultKey[self::sanitizeKey($location->$getter())] = $location;
                    }

                    $resultSorting[$location->getSorting()] = $location;

                    if ($location->getSorting()) {
                        $sortingUsed = true;
                    }
                }
            }

            $result = $resultKey;
            if ($sortingUsed) {
                $result = $resultSorting;
            }

            if ($sortDirection == 'desc') {
                krsort($result);
            } else {
                ksort($result);
            }
        }

        return $result;
    }


    /**
     * Sanitize key according to DIN 5007-2
     * @param string $key
     * @return string
     */
    public static function sanitizeKey(string $key): string {
        $aSearch   = array("Ä","ä","Ö","ö","Ü","ü","ß","-");
        $aReplace  = array("Ae","ae","Oe","oe","Ue","ue","ss"," ");
        return str_replace($aSearch, $aReplace, $key);
    }

}
