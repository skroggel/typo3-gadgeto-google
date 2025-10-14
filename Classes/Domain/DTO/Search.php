<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\Domain\DTO;

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

use Madj2k\GadgetoGoogle\Domain\Model\Category;


/**
 * Class Search
 *
 * @author Steffen Kroggel <mail@steffenkroggel.de>
 * @copyright Steffen Kroggel <mail@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class Search
{

	/**
	 * @var string
	 */
	protected string $addressQuery = '';


    /**
     * @var string
     */
    protected string $lngLatQuery = '';


	/**
	 * @var int
	 */
	protected int $radius = 0;



    /**
     * @var \Madj2k\GadgetoGoogle\Domain\Model\Category|null
     */
    protected Category|null $category = null;



    /**
     * Get lngLatQuery
     *
     * @return string
     */
    public function getLngLatQuery(): string
    {
        return $this->lngLatQuery;
    }


    /**
     * Set lngLatQuery
     *
     * @param string $lngLatQuery
     * @return void
     */
    public function setLngLatQuery(string $lngLatQuery): void
    {
        $this->lngLatQuery = $lngLatQuery;
    }


	/**
	 * Get addressQuery
	 *
	 * @return string
	 */
	public function getAddressQuery(): string
	{
		return $this->addressQuery;
	}


	/**
	 * Set addressQuery
	 *
	 * @param string $addressQuery
	 * @return void
	 */
	public function setAddressQuery(string $addressQuery): void
	{
		$this->addressQuery = $addressQuery;
	}


	/**
	 * Get radius
	 *
	 * @return int
	 */
	public function getRadius(): int
	{
		return $this->radius;
	}


	/**
	 * Set radius
	 *
	 * @param int|null $radius
	 * @return void
	 */
	public function setRadius(?int $radius): void
	{
		$this->radius = (int) $radius;
	}


    /**
     * Is active
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        if ($this->getAddressQuery() || $this->getLngLatQuery()) {
            return true;
        }

        return false;
    }


    /**
     * Get category
     *
     * @return \Madj2k\GadgetoGoogle\Domain\Model\Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }


    /**
     * Set category
     *
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Category $category
     * @return void
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

}
