<?php
defined('TYPO3') or die('Access denied.');
call_user_func(
	function($extKey)
	{

		//===========================================================================
		// Add fields
		//===========================================================================
        $ll = 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:';
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category',
			[
				'tx_gadgetogoogle_style' => [
					'exclude' => true,
					'label' => $ll . 'sys_category.tx_gadgetogoogle_style',
					'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'default' => 'default',
                        'items' => [
                            [
                                'label' => 'Default',
                                'value' => 'default',
                            ],
                        ],
					],
				],
            ]
		);

        //  add to palette
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
			'sys_category',
			'tx_gadgetogoogle_style',
			'',
			'before:images'
		);
	},
	'gadgeto_google'
);
