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
     * @var int
     */
    protected int $identifier = 0;


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
     * @var int
     */
    protected int $page = 1;


    /**
     * @var \Madj2k\GadgetoGoogle\Domain\Model\Category|null
     */
    protected Category|null $category = null;


    /**
     * Get identifier
     *
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }


    /**
     * Set identifier
     *
     * @param int $identifier
     * @return void
     */
    public function setIdentifier(int $identifier): void
    {
        $this->identifier = $identifier;
    }


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
	 * @param int $radius
	 * @return void
	 */
	public function setRadius(int $radius): void
	{
		$this->radius = $radius;
	}


    /**
     * Get page
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }


    /**
     * Set page
     *
     * @param int $page
     * @return void
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
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
     * @param \Madj2k\GadgetoGoogle\Domain\Model\Category|null $category
     * @return void
     */
    public function setCategory(Category|null $category): void
    {
        $this->category = $category;
    }


    /**
     * Is active
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        if ($this->getAddressQuery() || $this->getLngLatQuery() || $this->getCategory()) {
            return true;
        }

        return false;
    }

}
