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
use Madj2k\GadgetoGoogle\Traits\PersonTrait;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

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
    use PersonTrait;
    use AddressTrait;

    /**
     * @var string
     */
    protected string $seoLabel = '';


    /**
     * @var string
     */
    protected string $phone = '';


    /**
     * @var string
     */
    protected string $mobile = '';


    /**
     * @var string
     */
    protected string $fax = '';


    /**
     * @var string
     */

    protected string $email = '';


    /**
     * @var string
     */
    protected string $url = '';


    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    protected ?FileReference $image = null;


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


    /**
     * Returns the seoLabel
     *
     * @return string
     */
    public function getSeoLabel(): string
    {
        return $this->seoLabel;
    }


    /**
     * Sets the seoLabel
     *
     * @param string $seoLabel
     * @return void
     */
    public function setSeoLabel(string $seoLabel): void
    {
        $this->seoLabel = $seoLabel;
    }


    /**
     * Returns the phone
     *
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }


    /**
     * Sets the phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }


    /**
     * Returns the mobile
     *
     * @return string
     */
    public function getMobile(): string
    {
        return $this->mobile;
    }


    /**
     * Sets the mobile
     *
     * @param string $mobile
     * @return void
     */
    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }


    /**
     * Returns the fax
     *
     * @return string
     */
    public function getFax(): string
    {
        return $this->fax;
    }


    /**
     * Sets the fax
     *
     * @param string $fax
     * @return void
     */
    public function setFax(string $fax): void
    {
        $this->fax = $fax;
    }


    /**
     * Returns the email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    /**
     * Returns the url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }


    /**
     * Sets the url
     *
     * @param string $url
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }


    /**
     * Returns the image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|null $image
     */
    public function getImage():? FileReference
    {
        return $this->image;
    }


    /**
     * Sets the image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function setImage(FileReference $image): void
    {
        $this->image = $image;
    }


}
