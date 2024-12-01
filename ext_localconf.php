<?php
defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extKey)
	{

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extKey,
            'Map',
            [\Madj2k\GadgetoGoogle\Controller\MapController::class => 'show'],

            // non-cacheable actions
            [\Madj2k\GadgetoGoogle\Controller\MapController::class => 'show'],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extKey,
            'List',
            [\Madj2k\GadgetoGoogle\Controller\LocationController::class => 'list'],

            // non-cacheable actions
            [\Madj2k\GadgetoGoogle\Controller\LocationController::class => ''],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        //=================================================================
        // Hooks
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['example'] =
            \Madj2k\GadgetoGoogle\Hooks\TceMainHooks::class;

        //=================================================================
        // Register Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Madj2k']['GadgetoGoogle']['writerConfiguration'] = [

            \TYPO3\CMS\Core\Log\LogLevel::WARNING => [
                // add a FileWriter
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => [
                    // configuration for the writer
                    'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath()  . '/log/tx_gadgetogoogle.log'
                ]
            ],
        ];

    },
	'gadgeto_google'
);


