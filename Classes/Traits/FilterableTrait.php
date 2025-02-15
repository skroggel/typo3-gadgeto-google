<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Traits;

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

use Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface;
use Madj2k\GadgetoGoogle\Domain\Model\FilterCategory;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FilterableTrait
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
trait FilterableTrait
{
    use GeoPositionTrait;


    /**
     * @var string
     */
    protected string $label = '';


    /**
     * @var int
     */
    protected int $sorting = 0;


    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\GadgetoGoogle\Domain\Model\FilterCategory>|\TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy|null
    */
    protected ObjectStorage|LazyLoadingProxy|null $filterCategory = null;


    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\GadgetoGoogle\Domain\Model\Category>|\TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy|null
     */
    protected ObjectStorage|LazyLoadingProxy|null $categories = null;


    /**
     * Injection
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage
     * @return void
     */
    public function injectObjectStorage(ObjectStorage $objectStorage):void
    {
        $this->filterCategory = $this->filterCategory ?? $objectStorage;
        $this->categories = $this->categories ?? $objectStorage;
    }


    /**
     * Returns the label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }


    /**
     * Sets the label
     *
     * @param string $label
     * @return void
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }


    /**
     * Returns the sorting
     *
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }


    /**
     * Sets the sorting
     *
     * @param int $sorting
     * @return void
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }


    /**
     * Returns the filterCategory
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\GadgetoGoogle\Domain\Model\FilterCategory> $filterCategory
     */
    public function getFilterCategory(): ObjectStorage
    {
        if ($this->filterCategory instanceof LazyLoadingProxy) {
            $this->filterCategory->_loadRealInstance();
        }

        if ($this->filterCategory instanceof ObjectStorage) {
            return $this->filterCategory;
        }

        return $this->filterCategory= new ObjectStorage();
    }


    /**
     * Sets the filterCategory
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\GadgetoGoogle\Domain\Model\FilterCategory> $filterCategory
     * @return void
     */
    public function setFilterCategory(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $filterCategory): void
    {
        $this->filterCategory = $filterCategory;
    }


    /**
     * Adds a category
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Category $category
     * @return void
     */
    public function addCategory(Category $category): void
    {
        $this->categories->attach($category);
    }


    /**
     * Removes a category
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Category $category
     * @return void
     */
    public function removeCategory(Category $category): void
    {
        $this->categories->detach($category);
    }


    /**
     * Returns the categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\GadgetoGoogle\Domain\Model\Category> $categories
     */
    public function getCategories(): ObjectStorage
    {
        if ($this->categories instanceof LazyLoadingProxy) {
            $this->categories->_loadRealInstance();
        }

        if ($this->categories instanceof ObjectStorage) {
            return $this->categories;
        }

        return $this->categories = new ObjectStorage();
    }


    /**
     * Sets the categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\GadgetoGoogle\Domain\Model\Category> $categories
     * @return void
     */
    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

}
