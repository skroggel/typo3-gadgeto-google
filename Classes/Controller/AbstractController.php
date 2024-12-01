<?php
declare(strict_types=1);

namespace Madj2k\GadgetoGoogle\Controller;

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

use Madj2k\CatSearch\Domain\Model\FilterableInterface;
use Madj2k\GadgetoGoogle\Domain\Repository\LocationRepository;

/**
 * Class AbstractController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class AbstractController extends  \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \Madj2k\GadgetoGoogle\Domain\Repository\LocationRepository|null
     */
    protected ?LocationRepository $locationRepository;


    /**
     * @param \Madj2k\GadgetoGoogle\Domain\Repository\LocationRepository $locationRepository
     * @return void
     */
    public function injectLocationRepository(LocationRepository $locationRepository): void
    {
        $this->locationRepository = $locationRepository;
    }


    /**
     * Assign default variables to view
     */
    protected function initializeView(): void
    {
        $this->view->assign('data', $this->request->getAttribute('currentContentObject')->data);

        // check for layout - and for layout of item for detail view!
        $layout = $this->settings['layout'] ?? 'default';
        if ($this->arguments->hasArgument('item')) {
            $item = $this->arguments->getArgument('item')->getValue();

            if ($item instanceof FilterableInterface) {
                $layout = $item->getLayout();
            }
        }

        // set layout specific settings in separate array
        if (
            (isset($this->settings['layoutOverride'][$layout]))
            && (is_array($this->settings['layoutOverride'][$layout]))
        ){
            $layoutSettings = $this->settings['layoutOverride'][$layout];
            $settings = $this->settings;
            unset($settings['layoutOverride']);
            $this->view->assign('settingsForLayout', array_merge($settings, $layoutSettings));
        }
    }

}
