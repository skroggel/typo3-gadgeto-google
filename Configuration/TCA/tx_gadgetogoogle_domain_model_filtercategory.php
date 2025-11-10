<?php
declare(strict_types=1);

$ll = 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:';
return [
    'ctrl' => [
        'title'	=> $ll . 'tx_gadgetogoogle_domain_model_filtercategory',
        'label' => 'label',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'default_sortby' => 'ORDER BY label ASC',

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'label',
        'iconfile' => 'EXT:gadgeto_google/Resources/Public/Icons/tx_gadgetogoogle_domain_model_filtercategory.svg'
    ],
    'types' => [
        '1' => ['showitem' => 'label,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime
                '],
    ],
    'columns' => [

        'sys_language_uid' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'foreign_table' => 'tx_gadgetogoogle_domain_model_filtercategory',
                'foreign_table_where' => 'AND {#tx_gadgetogoogle_domain_model_filtercategory}.{#pid}=###CURRENT_PID### AND {#tx_gadgetogoogle_domain_model_filtercategory}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'label' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_filtercategory.label',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'required' => true,
            ],
        ],
    ]
];

