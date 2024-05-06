<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Domain\Model;

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

use Madj2k\GadgetoGoogle\Traits\AddressTrait;
use Madj2k\GadgetoGoogle\Traits\FilterableTrait;

/**
 * Class Location
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Location extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements FilterableInterface
{

    use FilterableTrait;
    use AddressTrait;


    /**
     * Adds a filterCategory
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\FilterCategory $filterCategory
     * @return void
     */
    public function addFilterCategory(FilterCategory $filterCategory): void
    {
        $this->filterCategory->attach($filterCategory);
    }


    /**
     * Removes a filterCategory
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\FilterCategory $filterCategory
     * @return void
     */
    public function removeFilterCategory(FilterCategory $filterCategory): void
    {
        $this->filterCategory->detach($filterCategory);
    }

}
