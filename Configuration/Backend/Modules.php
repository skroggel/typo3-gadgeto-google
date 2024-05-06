<?php
declare(strict_types=1);

/**
 * Definitions for modules provided by EXT:examples
 */
return [
    // Example for a module registration with Extbase controller
    'tools_gadgetogoogle' => [
        'parent' => 'tools',
        'position' => ['after' => 'tools_csp'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/tools/gadgeto-google',
        'labels' => 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_mod.xlf',
        // Extbase-specific configuration telling the TYPO3 Core to bootstrap Extbase
        'extensionName' => 'GadgetoGoogle',
        'controllerActions' => [
            \Madj2k\GadgetoGoogle\Controller\AdminModuleController::class => [
                'keys', 'saveKeys'
            ],
        ],
    ],
];

