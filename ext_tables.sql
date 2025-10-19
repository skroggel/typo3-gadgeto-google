#
# Table structure for table 'tx_gadgetogoogle_domain_model_location'
#
CREATE TABLE tx_gadgetogoogle_domain_model_location
(
	label                varchar(255) DEFAULT '' NOT NULL,
	sub_label            varchar(255) DEFAULT '' NOT NULL,
	slug 						     varchar(2048) DEFAULT '' NOT NULL,

	gender               int(11) unsigned NOT NULL default '99',
	title                varchar(255) DEFAULT '' NOT NULL,
	firstname            varchar(255) DEFAULT '' NOT NULL,
	lastname             varchar(255) DEFAULT '' NOT NULL,
	company              varchar(255) DEFAULT '' NOT NULL,
	street               varchar(255) DEFAULT '' NOT NULL,
	street_number        varchar(255) DEFAULT '' NOT NULL,
	zip                  varchar(255) DEFAULT '' NOT NULL,
	city                 varchar(255) DEFAULT '' NOT NULL,
	country              varchar(255) DEFAULT '' NOT NULL,

	phone                varchar(255) DEFAULT '' NOT NULL,
	mobile               varchar(255) DEFAULT '' NOT NULL,
	fax                  varchar(255) DEFAULT '' NOT NULL,
	email                varchar(255) DEFAULT '' NOT NULL,
	url                  varchar(255) DEFAULT '' NOT NULL,
	image                int(11) unsigned NOT NULL default '0',

	address_addition_api varchar(255) DEFAULT '' NOT NULL,
	manual_lng_lat       int(4) unsigned NOT NULL default '0',

	longitude            decimal(15, 8)          NOT NULL,
	latitude             decimal(15, 8)          NOT NULL,

	filter_category      int(11) unsigned NOT NULL default '0',
	sorting              int(11) unsigned NOT NULL default '0',

	KEY                  label (label),
	KEY                  filter_category (filter_category),
	KEY                  log_lat (longitude, latitude)
);


#
# Table structure for table 'tx_gadgetogoogle_domain_model_filtercategory'
#
CREATE TABLE tx_gadgetogoogle_domain_model_filtercategory
(
	label varchar(255) DEFAULT '' NOT NULL,

	KEY   label (label),
);


CREATE TABLE sys_category
(
	tx_gadgetogoogle_style varchar(255) DEFAULT 'default' NOT NULL,
);

