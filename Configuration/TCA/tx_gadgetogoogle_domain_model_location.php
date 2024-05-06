<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location',
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
		'iconfile' => 'EXT:gadgeto_google/Resources/Public/Icons/tx_gadgetogoogle_domain_model_location.gif'
	],
	'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, label, --palette--;;filter, --palette--;;address_1, --palette--;;address_2, --palette--;;geo_position, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'palettes' => [
        'address_1' => [
            'showitem' => 'street, street_number',
        ],
        'address_2' => [
            'showitem' => 'zip, city',
        ],
        'filter' => [
            'showitem' => 'filter_category',
        ],
        'geo_position' => [
            'showitem' => 'longitude,latitude',
        ],
    ],
	'columns' => [

		'sys_language_uid' => [
			'exclude' => 0,
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
			'exclude' => 0,
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
			'exclude' => 0,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'starttime' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
                'renderType' => 'inputDateTime',
				'size' => 13,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
			],
		],
		'endtime' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
                'renderType' => 'inputDateTime',
				'size' => 13,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
			],
		],
		'label' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location.label',
			'config' => [
				'type' => 'input',
				'eval' => 'required,trim'
			],
		],
        'street' => [
            'exclude' => 0,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location.street',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'street_number' => [
            'exclude' => 0,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location.street_number',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'eval' => 'trim'
            ],
        ],
        'zip' => [
            'exclude' => 0,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location.zip',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'eval' => 'trim'
            ],
        ],
        'city' => [
            'exclude' => 0,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location.city',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'longitude' => [
            'l10n_mode' => 'exclude',
            'exclude' => 0,
            'label' => 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location.longitude',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
        'latitude' => [
            'l10n_mode' => 'exclude',
            'exclude' => 0,
            'label' => 'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location.latitude',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
        'filter_category' => [
            'label'=>'LLL:EXT:gadgeto_google/Resources/Private/Language/locallang_db.xlf:tx_gadgetogoogle_domain_model_location.filter_category',
            'exclude' => 0,
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_gadgetogoogle_domain_model_filtercategory',
                'foreign_table_where' => 'AND tx_gadgetogoogle_domain_model_filtercategory.pid=###CURRENT_PID### AND tx_gadgetogoogle_domain_model_filtercategory.hidden = 0 AND tx_gadgetogoogle_domain_model_filtercategory.deleted = 0 AND tx_gadgetogoogle_domain_model_filtercategory.sys_language_uid IN (-1,0) ORDER BY label ASC',
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
	]
];

