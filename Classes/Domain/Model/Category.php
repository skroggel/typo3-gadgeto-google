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

/**
 * Class SysCategory
 *
 * @author Steffen Kroggel <mail@steffenkroggel.de>
 * @copyright Steffen Kroggel <mail@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Category extends \TYPO3\CMS\Extbase\Domain\Model\Category
{
    /**
     * @var string
     */
    protected string $txGadgetogoogleStyle= '';


    /**
     * Gets the txGadgetoGoogleStyle
     *
     * @return string
     */
    public function getTxGadgetogoogleStyle(): string
    {
        return $this->txGadgetogoogleStyle;
    }


    /**
     * Sets the txGadgetoGoogleStyle
     *
     * @param string $txGadgetogoogleStyle
     * @return void
     */
    public function setTxGadgetogoogleStyle(string $txGadgetogoogleStyle): void
    {
        $this->txGadgetogoogleStyle = $txGadgetogoogleStyle;
    }

}
