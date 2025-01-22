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

use Madj2k\GadgetoGoogle\Domain\Model\Location;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GroupByCategoryViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GroupByCategoryViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('items', QueryResultInterface::class, 'The object-storage.');
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

        /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage $items  */
        $items = $arguments['items'];

        $result = [];
        if ($items) {
            /** @var \Madj2k\GadgetoGoogle\Domain\Model\Location $item */
            foreach ($items as $item) {
                foreach ($item->getCategories() as $category) {
                    $tempResult = self::buildCategoryHierarchy($category, [], $item);
                    $result = self::arrayMergeRecursiveDistinct($result, $tempResult);
                }
            }
        }

        return $result;

    }


    /**
     * Build category hierarchy
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     * @param array $subResult
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Location|null $location
     * @return array|array[]
     */
    protected static function buildCategoryHierarchy (Category $category, array $subResult, ?Location $location = null): array
    {

        $result = [
             $category->getUid() => [
                'object' =>  $category,
                'children' => $subResult,
                'locations' => []
            ]
        ];

        if ($location) {
            $result[$category->getUid()]['locations'][$location->getUid()] = $location;
        }

        if ($parentCategory = $category->getParent()) {
             return self::buildCategoryHierarchy($parentCategory, $result);
        }

        return $result;
    }


    /**
     * Merge recursive distinct
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel@danielsmedegaardbuus.dk>
     * @author Gabriel Sobrinho <gabriel.sobrinho@gmail.com>
     * @see https://www.php.net/manual/en/function.array-merge-recursive.php#92195
     */
    static function arrayMergeRecursiveDistinct (array &$array1, array &$array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::arrayMergeRecursiveDistinct( $merged [$key], $value );
            } else{
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
