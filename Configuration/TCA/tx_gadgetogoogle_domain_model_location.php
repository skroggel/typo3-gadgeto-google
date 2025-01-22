<?php
declare(strict_types=1);
use Madj2k\GadgetoGoogle\Utilities\TcaUtility;

$ll = 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:';
return [
	'ctrl' => [
		'title'	=> $ll . 'tx_gadgetogoogle_domain_model_location',
		'label' => 'label',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
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
		'iconfile' => 'EXT:gadgeto_google/Resources/Public/Icons/tx_gadgetogoogle_domain_model_location.svg'
	],
	'types' => [
        '1' => ['showitem' =>
            TcaUtility::removeFieldsByExtConf(
                'label, company, --palette--;;person, --palette--;;address, --palette--;;phone, --palette--;;contact, image, --palette--;;geo_position, --palette--;;filter,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime
                '
            )
        ],
    ],
    'palettes' => [
        'person' => [
            'showitem' => TcaUtility::removeFieldsByExtConf('gender, title, --linebreak--, firstname, lastname'),
        ],
        'address' => [
            'showitem' => TcaUtility::removeFieldsByExtConf('street, street_number, --linebreak--, zip, city, --linebreak--, country, --linebreak--, address_addition'),
        ],
        'phone' => [
            'showitem' => TcaUtility::removeFieldsByExtConf('phone, mobile, --linebreak--,fax'),
        ],
        'contact' => [
            'showitem' => TcaUtility::removeFieldsByExtConf('email, url'),
        ],
        'filter' => [
            'showitem' => TcaUtility::removeFieldsByExtConf('categories, --linebreak--, filter_category'),
        ],
        'geo_position' => [
            'showitem' => TcaUtility::removeFieldsByExtConf('longitude,latitude'),
        ],
    ],
	'columns' => [

		'sys_language_uid' => [
			'exclude' => false,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => [
					['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
					['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
				],
                'default' => 0
			],
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => false,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['', 0],
				],
				'foreign_table' => 'tx_gadgetogoogle_domain_model_location',
				'foreign_table_where' => 'AND tx_gadgetogoogle_domain_model_location.pid=###CURRENT_PID### AND tx_gadgetogoogle_domain_model_location.sys_language_uid IN (-1,0)',
			],
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough',
			],
		],

		'hidden' => [
			'exclude' => false,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'starttime' => [
			'exclude' => false,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
                'renderType' => 'inputDateTime',
				'size' => 13,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
			],
		],
		'endtime' => [
			'exclude' => false,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
                'renderType' => 'inputDateTime',
				'size' => 13,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
			],
		],
		'label' => [
			'exclude' => false,
			'label' => $ll . 'tx_gadgetogoogle_domain_model_location.label',
			'config' => [
				'type' => 'input',
				'eval' => 'required,trim'
			],
		],
        'title' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.title',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'eval' => 'trim'
            ],
        ],
        'gender' => [
            'label'=>$ll . 'tx_gadgetogoogle_domain_model_location.gender',
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'config'=>[
                'type' => 'select',
                'renderType' => 'selectSingle',
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 99,
                'items' => [
                    [$ll . 'tx_gadgetogoogle_domain_model_location.gender.0', '0'],
                    [$ll . 'tx_gadgetogoogle_domain_model_location.gender.1', '1'],
                    [$ll . 'tx_gadgetogoogle_domain_model_location.gender.2', '2'],
                    [$ll . 'tx_gadgetogoogle_domain_model_location.gender.99', '99'],
                ],
            ],
        ],
        'firstname' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.firstname',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'lastname' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.lastname',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'company' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.company',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required'
            ],
        ],
        'street' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.street',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'street_number' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.street_number',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'eval' => 'trim'
            ],
        ],
        'zip' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.zip',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'city' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.city',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'country' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.country',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0]
                ],
                'default' => TcaUtility::getDefaultCountryByExtConf(),
                'foreign_table' => 'static_countries',
                'allowNonIdValues' => true,
                'foreign_table_where' => 'ORDER BY static_countries.cn_iso_2',
                'itemsProcFunc' => \SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider\TcaSelectItemsProcessor::class . '->translateCountriesSelector',
                'itemsProcFunc_config' => [
                    'indexField' => 'cn_iso_2',
                ],
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1
            ],
        ],
        'address_addition' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.address_addition',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'phone' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.phone',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'fax' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.fax',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'mobile' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.mobile',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.email',
            'config' => [
                'type' => 'email',
                'eval' => 'trim',
            ],
        ],
        'url' => [
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.url',
            'config' => [
                'type' => 'link',
                'eval' => 'trim',
            ],
        ],
        'longitude' => [
            'l10n_mode' => 'exclude',
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.longitude',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
        'latitude' => [
            'l10n_mode' => 'exclude',
            'exclude' => false,
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.latitude',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
        'distance' => [
            'config' => [
                'type' => 'none',
            ],
        ],
        'categories' => [
            'config' => [
                'type' => 'category',
                'foreign_table_where' => 'AND {#sys_category}.{#sys_language_uid} IN (-1, 0) AND {#sys_category}.{#pid} = ###CURRENT_PID###',
                'treeConfig' => [
                    'appearance' => [
                        'nonSelectableLevels' => '0,1'
                    ],
                ]
            ],
        ],
        /* deprecated */
        'filter_category' => [
            'label'=> $ll . 'tx_gadgetogoogle_domain_model_location.filter_category',
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_gadgetogoogle_domain_model_filtercategory',
                'foreign_table_where' => 'AND {#tx_gadgetogoogle_domain_model_filtercategory}.{#pid}=###CURRENT_PID### AND {#tx_gadgetogoogle_domain_model_filtercategory}.{#hidden} = 0 AND {#tx_gadgetogoogle_domain_model_filtercategory}.{#deleted} = 0 AND {#tx_gadgetogoogle_domain_model_filtercategory}.{#sys_language_uid} IN (-1,0) ORDER BY label ASC',
                'minitems' => 1,
                'maxitems' => 9999,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => false,
                    ],
                ],
            ],
        ],
        'image' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_gadgetogoogle_domain_model_location.image',
            'config' => [
                'type' => 'file',
                'allowed' => ['jpeg','jpg','png','gif','svg','webp'],
                'maxitems' => 1,
            ]
        ],
	]
];

