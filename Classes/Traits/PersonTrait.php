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

/**
 * Class PersonTrait
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
trait PersonTrait
{

    /**
     * @var int
     */
    protected int $gender;


    /**
     * @var string
     */
    protected string $title = '';


    /**
     * @var string
     */
    protected string $firstname = '';


    /**
     * @var string
     */
    protected string $lastname = '';


    /**
     * Returns the gender
     *
     * @return int
     */
    public function getGender(): int
    {
        return $this->gender;
    }


    /**
     * Sets the gender
     *
     * @param int $gender
     * @return void
     */
    public function setGender(int $gender): void
    {
        $this->gender = $gender;
    }


    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    /**
     * Returns the firstname
     *
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }


    /**
     * Sets the firstname
     *
     * @param string $firstname
     * @return void
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }


    /**
     * Returns the lastname
     *
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }


    /**
     * Sets the lastname
     *
     * @param string $lastname
     * @return void
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

}
