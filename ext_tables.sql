CREATE TABLE tt_content (
  tx_sudhaus7newspage_from  int(11) unsigned NOT NULL DEFAULT '0',
  tx_sudhaus7newspage_to  int(11) unsigned NOT NULL DEFAULT '0',
  tx_sudhaus7newspage_tag  int(5) unsigned NOT NULL DEFAULT '0',
  tx_sudhaus7newspage_showdate  int(1) unsigned NOT NULL DEFAULT '0',
  tx_sudhaus7newspage_showtime  int(1) unsigned NOT NULL DEFAULT '0',
  tx_sudhaus7newspage_highlight  int(1) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_sudhaus7newspage_domain_model_tag (
  uid int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  category varchar(255) NOT NULL DEFAULT '',
  tstamp int(11) unsigned NOT NULL DEFAULT '0',
  crdate int(11) unsigned NOT NULL DEFAULT '0',
  cruser_id int(11) unsigned NOT NULL DEFAULT '0',
  deleted tinyint(4) unsigned NOT NULL DEFAULT '0',
  hidden tinyint(4) unsigned NOT NULL DEFAULT '0',
  t3_origuid int(11) NOT NULL DEFAULT '0',
  sys_language_uid int(11) NOT NULL DEFAULT '0',
  l10n_parent int(11) NOT NULL DEFAULT '0',
  l10n_diffsource mediumblob,
  relation int(11) NOT NULL DEFAULT '0',

  parent_tag int(11) NOT NULL DEFAULT '0',
  map int(6) NOT NULL DEFAULT '0',
  geodata varchar(64) NOT NULL DEFAULT '',
  georatio int(11) NOT NULL DEFAULT '0',
  countrydesc text,
  churchdesc text,
  staffdesc text,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY idxrelation (relation),
  KEY idxparenttag (parent_tag),
  KEY language (l10n_parent,sys_language_uid)
);

CREATE TABLE tx_sudhaus7newspage_domain_tag_mm (
  uid_local int(10) unsigned NOT NULL DEFAULT '0',
  uid_foreign int(10) unsigned NOT NULL DEFAULT '0',
  sorting int(10) unsigned NOT NULL DEFAULT '0',
  sorting_foreign int(10) unsigned NOT NULL DEFAULT '0',
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);


#
# Table structure for table cf_sudhaus7newspage_pagecache
CREATE TABLE cf_sudhaus7newspage_pagecache (
    id int(11) unsigned NOT NULL auto_increment,
    identifier varchar(250) DEFAULT '' NOT NULL,
    expires int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    content mediumblob,
    lifetime int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (id),
    KEY cache_id (identifier)
) ENGINE=InnoDB;
