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
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
     * @const string
     */
    protected const string SESSION_STORAGE = 'gadgetoGoogle';


    /**
     * @var \Madj2k\GadgetoGoogle\Domain\Repository\LocationRepository|null
     */
    protected ?LocationRepository $locationRepository;


    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer|null $currentContentObject
     */
    protected ?ContentObjectRenderer $currentContentObject = null;


    /**
     * @var \TYPO3\CMS\Core\Site\Entity\SiteLanguage|null
     */
    protected ?SiteLanguage $siteLanguage = null;


    /**
     * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface|null
     */
    protected ?FrontendInterface $cache = null;


    /**
     * @param \Madj2k\GadgetoGoogle\Domain\Repository\LocationRepository $locationRepository
     * @return void
     */
    public function injectLocationRepository(LocationRepository $locationRepository): void
    {
        $this->locationRepository = $locationRepository;
    }


    /**
     * @param \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface $cache
     * @return void
     */
    public function injectCache(FrontendInterface $cache): void
    {
        $this->cache = $cache;
    }


    /**
     * Set globally used objects
     */
    protected function initializeAction(): void
    {
        $this->currentContentObject = $this->request->getAttribute('currentContentObject');
        $this->siteLanguage = $this->request->getAttribute('language');

        if ($this->arguments->hasArgument('search')) {
            $propertyMappingConfiguration = $this->arguments->getArgument('search')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowAllProperties();
        }
    }


    /**
     * Assign default variables to view
     */
    protected function initializeView(): void
    {
        $this->view->assign('data', $this->currentContentObject->data);

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


    /**
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser(): FrontendUserAuthentication
    {
        // This will create an anonymous frontend user if none is logged in
        return $this->request->getAttribute('frontend.user');
    }


    /**
     * Retrieves the session storage data.
     *
     * @return array The session storage data.
     */
    protected function getSessionStorage(): array
    {
        if ($data = $this->getFrontendUser()->getKey('ses', $this->getSessionStorageKey())) {
            return unserialize($data);
        }

        return [];
    }


    /**
     * Stores the session storage data.
     *
     * @param mixed $data The session storage data.
     * @return void
     */
    protected function setSessionStorage(mixed $data): void
    {
        $this->getFrontendUser()->setKey('ses', $this->getSessionStorageKey(), serialize($data));
        $this->getFrontendUser()->storeSessionData();
    }


    /**
     * Constructs and returns a unique session storage key based on a constant prefix
     * and the unique identifier (UID) of the current content object.
     *
     * @return string
     */
    protected function getSessionStorageKey(): string {
        return self::SESSION_STORAGE;
    }

}
