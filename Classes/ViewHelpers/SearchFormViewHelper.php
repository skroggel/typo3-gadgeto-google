<?php
declare(strict_types=1);
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

use TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper;

/**
 * Class SearchFormViewHelper
 *
 * @author Steffen Kroggel <mail@steffenkroggel.de>
 * @copyright Steffen Kroggel <mail@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SearchFormViewHelper extends FormViewHelper
{
	/**
	 * Render the form.
	 *
	 * @return string rendered form
	 */
	public function render(): string
	{
		$this->setFormActionUri();
		if (isset($this->arguments['method']) && strtolower($this->arguments['method']) === 'get') {
			$this->tag->addAttribute('method', 'get');
		} else {
			$this->tag->addAttribute('method', 'post');
		}

		if (isset($this->arguments['novalidate']) && $this->arguments['novalidate'] === true) {
			$this->tag->addAttribute('novalidate', 'novalidate');
		}

		$this->addFormObjectNameToViewHelperVariableContainer();
		$this->addFormObjectToViewHelperVariableContainer();
		$this->addFieldNamePrefixToViewHelperVariableContainer();
		$this->addFormFieldNamesToViewHelperVariableContainer();

		$this->tag->setContent($this->renderChildren());
		$this->removeFieldNamePrefixFromViewHelperVariableContainer();
		$this->removeFormObjectFromViewHelperVariableContainer();
		$this->removeFormObjectNameFromViewHelperVariableContainer();
		$this->removeFormFieldNamesFromViewHelperVariableContainer();
		$this->removeCheckboxFieldNamesFromViewHelperVariableContainer();
		return $this->tag->render();
	}
}
