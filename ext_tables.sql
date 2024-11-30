#
# Table structure for table 'tx_gadgetogoogle_domain_model_location'
#
CREATE TABLE tx_gadgetogoogle_domain_model_location
(
	label           varchar(255) DEFAULT '' NOT NULL,

	company         varchar(255) DEFAULT '' NOT NULL,
	street          varchar(255) DEFAULT '' NOT NULL,
	street_number   varchar(255) DEFAULT '' NOT NULL,
	zip             varchar(255) DEFAULT '' NOT NULL,
	city            varchar(255) DEFAULT '' NOT NULL,
	country         varchar(255) DEFAULT '' NOT NULL,

	longitude       decimal(15, 8)          NOT NULL,
	latitude        decimal(15, 8)          NOT NULL,
	filter_category int(11) unsigned NOT NULL default '0',

	KEY             label (label),
	KEY             filter_category (filter_category),
	KEY             log_lat (longitude, latitude)
);


#
# Table structure for table 'tx_gadgetogoogle_domain_model_filtercategory'
#
CREATE TABLE tx_gadgetogoogle_domain_model_filtercategory
(
	label    varchar(255) DEFAULT '' NOT NULL,

	KEY      label (label),
);

