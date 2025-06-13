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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
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
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected static function buildCategoryHierarchy (Category $category, array $subResult, ?Location $location = null): array
    {

        $languageUid = self::getCurrentLanguageUid();

        // get translated version - if available!
        $translatedCategory = self::getTranslatedCategory($category, $languageUid);
        $category = $translatedCategory ?? $category;

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
            // get translated version - if available!
            $translatedParent = self::getTranslatedCategory($parentCategory, $languageUid);
            return self::buildCategoryHierarchy($translatedParent ?? $parentCategory, $result, $location);
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


    /**
     * Get the translated category
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     * @param int $languageUid
     * @return \TYPO3\CMS\Extbase\Domain\Model\Category|null
     */
    private static function getTranslatedCategory(Category $category, int $languageUid): ?Category
    {
        if ($languageUid === 0 || (int)$category->_getProperty('sysLanguageUid') === $languageUid) {
            return $category;
        }

        $persistenceManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
        $query = $persistenceManager->createQueryForType(Category::class);

        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);

        $query->matching(
            $query->logicalAnd(
                $query->equals('l10nParent', $category->getUid()),
                $query->equals('sysLanguageUid', $languageUid),
            )
        );

        return $query->execute()->getFirst();
    }


    /**
     * @return int
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    private static function getCurrentLanguageUid(): int
    {
        /** @var \TYPO3\CMS\Core\Context\Context $context */
        $context = GeneralUtility::makeInstance(Context::class);

        /** @var \TYPO3\CMS\Core\Context\LanguageAspect $languageAspect */
        $languageAspect = $context->getAspect('language');
        return $languageAspect->getId();
    }
}
