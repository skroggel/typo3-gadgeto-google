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

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class AdminModuleController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class AdminModuleController extends  \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \TYPO3\CMS\Backend\Template\ModuleTemplateFactory|null
     */
    protected ?ModuleTemplateFactory $moduleTemplateFactory = null;


    /**
     * @var \TYPO3\CMS\Core\Registry|null
     */
    protected ?Registry $registry = null;


    /**
     * @param \TYPO3\CMS\Backend\Template\ModuleTemplateFactory $moduleTemplateFactory
     * @return void
     */
    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory):void
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }


    /**
     * @param \TYPO3\CMS\Core\Registry $registry
     * @return void
     */
    public function injectRegistry(Registry $registry):void
    {
        $this->registry = $registry;
    }


    /**
     * action keys
     *
     * @param array $googleMapsConfig
     * @return ResponseInterface
     */
	public function keysAction(array $googleMapsConfig = []): ResponseInterface
	{
        if (! $googleMapsConfig) {
            $googleMapsConfig = $this->registry->get(
                'gadgeto_google',
                'googleMapsConfig',
            );
        }

        $this->view->assignMultiple([
            'apiKey' => $googleMapsConfig['apiKey'] ?? '',
            'apiKeyMap' => $googleMapsConfig['apiKeyMap'] ?? '',
            'mapId' => $googleMapsConfig['mapId'] ?? ''
        ]);

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
	}


    /**
     * action keys
     *
     * @param array $googleMapsConfig
     * @return ResponseInterface
     */
    public function saveKeysAction(array $googleMapsConfig= []): ResponseInterface
    {
        if (
            (! $googleMapsConfig['apiKey'])
            || (! $googleMapsConfig['apiKeyMap'])
            || (! $googleMapsConfig['mapId'])
        ) {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'adminModuleController.error.valuesNotSet',
                    'gadgeto_google'
                ),
                '',
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('keys', null, null, ['googleMapsConfig' => $googleMapsConfig]);
        }

        $this->registry->set(
            'gadgeto_google',
            'googleMapsConfig',
            $googleMapsConfig
        );

        $this->addFlashMessage(
            LocalizationUtility::translate(
                'adminModuleController.message.valuesSaved',
                'gadgeto_google'
            ),
            '',
            \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::OK
        );

        return $this->redirect('keys', null, null, ['googleMapsConfig' => $googleMapsConfig]);
    }

}
