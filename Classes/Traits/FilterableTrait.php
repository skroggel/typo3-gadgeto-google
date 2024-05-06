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
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
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
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\GadgetoGoogle\Domain\Model\FilterCategory>|null
     */
    protected ?ObjectStorage $filterCategory = null;


    /**
     * Injection
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage
     * @return void
     */
    public function injectObjectStorage(ObjectStorage $objectStorage):void
    {
        $this->filterCategory = $objectStorage;
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
     * Returns the filterCategory
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\GadgetoGoogle\Domain\Model\FilterCategory> $filterCategory
     */
    public function getFilterCategory(): ObjectStorage
    {
        return $this->filterCategory;
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
    
}
