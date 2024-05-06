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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FilterableInterface
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface FilterableInterface extends GeoPositionInterface
{

    /**
     * Returns the label
     *
     * @return string
     */
    public function getLabel();


    /**
     * Sets the label
     *
     * @param string $label
     * @return void
     */
    public function setLabel(string $label);
    
       
    /**
     * Returns the searchCategory
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage $filterCategory
     */
    public function getFilterCategory(): ObjectStorage;


    /**
     * Sets the searchCategory
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $filterCategory
     * @return void
     */
    public function setFilterCategory(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $filterCategory): void;
}
