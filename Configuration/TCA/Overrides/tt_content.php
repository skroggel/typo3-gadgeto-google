<?php
defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extensionKey)
	{
        $pluginConfig = ['map'];
        foreach ($pluginConfig as $pluginName) {

            // register normal plugin
            $pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
                $extensionKey,
                \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($pluginName),
                'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_db.xlf:plugin.' .
                    \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($pluginName). '.title'
            );

            // add flexform to plugin
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
                '*', // wildcard when using third parameter, else use pluginSignature
                'FILE:EXT:'. $extensionKey . '/Configuration/FlexForms/' .
                \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($pluginName) . '.xml',
                $pluginSignature // third parameter adds flexform to content-element below, too!
            );
            
            
            // add content element
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
                'tt_content',
                'CType',
                [
                    'label' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_db.xlf:plugin.' .
                        TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($pluginName). '.title',
                    'value' => $pluginSignature,
                    'icon'  => 'EXT:' . $extensionKey . '/Resources/Public/Icons/Extension.svg',
                    'group' => $extensionKey,
                ]
            );

            // define TCA-fields
            // $GLOBALS['TCA']['tt_content']['types'][$pluginSignature] = $GLOBALS['TCA']['tt_content']['types']['list'];
            $GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] = '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;general,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
                    pi_flexform,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                    rowDescription,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
            ';
        }
	},
	'gadgeto_google'
);
