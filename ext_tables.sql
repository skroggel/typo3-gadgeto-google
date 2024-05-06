#
# Table structure for table 'tx_gadgetogoogle_domain_model_location'
#
CREATE TABLE tx_gadgetogoogle_domain_model_location
(

	uid              int(11) NOT NULL auto_increment,
	pid              int(11) DEFAULT '0' NOT NULL,

	label            varchar(255) DEFAULT '' NOT NULL,

	company          varchar(255) DEFAULT '' NOT NULL,
	street           varchar(255) DEFAULT '' NOT NULL,
	street_number    varchar(255) DEFAULT '' NOT NULL,
	zip              varchar(255) DEFAULT '' NOT NULL,
	city             varchar(255) DEFAULT '' NOT NULL,

	longitude        decimal(15,8) NOT NULL,
	latitude         decimal(15,8) NOT NULL,
	filter_category  int(11) unsigned NOT NULL default '0',

	tstamp           int(11) unsigned DEFAULT '0' NOT NULL,
	crdate           int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id        int(11) unsigned DEFAULT '0' NOT NULL,
	deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime        int(11) unsigned DEFAULT '0' NOT NULL,
	endtime          int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent      int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource  mediumblob,

	PRIMARY KEY (uid),
	KEY              parent (pid),
	KEY language (l10n_parent,sys_language_uid),
	KEY              label (label),
	KEY              filter_category (filter_category)
);


#
# Table structure for table 'tx_gadgetogoogle_domain_model_filtercategory'
#
CREATE TABLE tx_gadgetogoogle_domain_model_filtercategory
(
	uid              int(11) NOT NULL auto_increment,
	pid              int(11) DEFAULT '0' NOT NULL,

	label            varchar(255) DEFAULT '' NOT NULL,

	tstamp           int(11) unsigned DEFAULT '0' NOT NULL,
	crdate           int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id        int(11) unsigned DEFAULT '0' NOT NULL,
	deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime        int(11) unsigned DEFAULT '0' NOT NULL,
	endtime          int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent      int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource  mediumblob,

	PRIMARY KEY (uid),
	KEY              parent (pid),
	KEY language (l10n_parent,sys_language_uid),
	KEY              label (label)
);

