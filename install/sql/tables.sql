--
-- Table structure for table `iu4_contentrevisions`
--

DROP TABLE IF EXISTS `%prefix%contentrevisions`;
CREATE TABLE IF NOT EXISTS `%prefix%contentrevisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contents` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


--
-- Table structure for table `iu4_contents`
--

DROP TABLE IF EXISTS `%prefix%contents`;
CREATE TABLE IF NOT EXISTS `%prefix%contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `div` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contents` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `contenttype_id` int(11) NOT NULL DEFAULT '1',
  `is_global` tinyint(1) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `editor_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


--
-- Table structure for table `iu4_contents_users`
--

DROP TABLE IF EXISTS `%prefix%contents_users`;
CREATE TABLE IF NOT EXISTS `%prefix%contents_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_contenttypes`
--

DROP TABLE IF EXISTS `%prefix%contenttypes`;
CREATE TABLE IF NOT EXISTS `%prefix%contenttypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `classname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `iu4_contenttypes`
--

INSERT INTO `%prefix%contenttypes` (`id`, `name`, `description`, `classname`) VALUES
(1, 'Static HTML', 'Static HTML content', 'Html'),
(2, 'News/Blog', 'News or blog repeatable content', 'Repeatable');

--
-- Table structure for table `iu4_filerevisions`
--

DROP TABLE IF EXISTS `%prefix%filerevisions`;
CREATE TABLE IF NOT EXISTS `%prefix%filerevisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contents` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_files`
--

DROP TABLE IF EXISTS `%prefix%files`;
CREATE TABLE IF NOT EXISTS `%prefix%files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `checksum` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `editor_id` int(11) NOT NULL,
  `default` int(1) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`(333))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_hits`
--

DROP TABLE IF EXISTS `%prefix%hits`;
CREATE TABLE IF NOT EXISTS `%prefix%hits` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `os` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `browser` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referer` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referer_domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `returning` tinyint(1) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


--
-- Table structure for table `iu4_images`
--

DROP TABLE IF EXISTS `%prefix%images`;
CREATE TABLE IF NOT EXISTS `%prefix%images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default` tinyint(1) DEFAULT NULL,
  `order` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_languages`
--

DROP TABLE IF EXISTS `%prefix%languages`;
CREATE TABLE IF NOT EXISTS `%prefix%languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `iu4_languages`
--

INSERT INTO `%prefix%languages` (`id`, `name`, `slug`, `active`) VALUES
(1, 'English', 'en', 1);

--
-- Table structure for table `iu4_logs`
--

DROP TABLE IF EXISTS `%prefix%logs`;
CREATE TABLE IF NOT EXISTS `%prefix%logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `severity` enum('notice','warning','alert') COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_pages`
--

DROP TABLE IF EXISTS `%prefix%pages`;
CREATE TABLE IF NOT EXISTS `%prefix%pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `editor_id` int(11) DEFAULT NULL,
  `default` tinyint(1) NOT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`(333))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_pages_users`
--

DROP TABLE IF EXISTS `%prefix%pages_users`;
CREATE TABLE IF NOT EXISTS `%prefix%pages_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;



--
-- Table structure for table `iu4_permissions`
--

DROP TABLE IF EXISTS `%prefix%permissions`;
CREATE TABLE IF NOT EXISTS `%prefix%permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `iu4_permissions`
--

INSERT INTO `%prefix%permissions` (`id`, `key`, `name`) VALUES
(1, 'edit_templates', 'User can edit template files and assign templates to pages.'),
(2, 'add_pages', 'User can add new pages to the site.'),
(4, 'edit_all_pages', 'User can edit all pages.'),
(5, 'edit_global_contents', 'User can edit global contents.'),
(6, 'manage_users', 'User can add and edit users.'),
(7, 'manage_user_roles', 'User can add and edit user groups.'),
(8, 'edit_assets', 'User can edit his own assets.'),
(10, 'edit_all_assets', 'User can edit all assets.'),
(11, 'edit_settings', 'User can edit website settings.'),
(12, 'edit_license', 'User can manage license information.');


--
-- Table structure for table `iu4_permissions_userroles`
--

DROP TABLE IF EXISTS `%prefix%permissions_userroles`;
CREATE TABLE IF NOT EXISTS `%prefix%permissions_userroles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) DEFAULT NULL,
  `userrole_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=42 ;

--
-- Dumping data for table `iu4_permissions_userroles`
--

INSERT INTO `%prefix%permissions_userroles` (`id`, `permission_id`, `userrole_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(38, 8, 3),
(16, 3, 3),
(37, 5, 3),
(36, 4, 3),
(35, 2, 3),
(34, 1, 3),
(33, 8, 4),
(32, 2, 4);


--
-- Table structure for table `iu4_permissions_users`
--

DROP TABLE IF EXISTS `%prefix%permissions_users`;
CREATE TABLE IF NOT EXISTS `%prefix%permissions_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_phrases`
--

DROP TABLE IF EXISTS `%prefix%phrases`;
CREATE TABLE IF NOT EXISTS `%prefix%phrases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) DEFAULT NULL,
  `phrase` text COLLATE utf8_unicode_ci NOT NULL,
  `translation` text COLLATE utf8_unicode_ci NOT NULL,
  `filter` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_repeatableitems`
--

DROP TABLE IF EXISTS `%prefix%repeatableitems`;
CREATE TABLE IF NOT EXISTS `%prefix%repeatableitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timestamp` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `iu4_settings`
--

DROP TABLE IF EXISTS `%prefix%settings`;
CREATE TABLE IF NOT EXISTS `%prefix%settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `options` text COLLATE utf8_unicode_ci,
  `type` enum('text','select','radio','checkbox') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `group` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=52 ;

--
-- Dumping data for table `iu4_settings`
--

INSERT INTO `%prefix%settings` (`id`, `name`, `value`, `options`, `type`, `group`, `label`, `description`) VALUES
(1, 'website_title', 'yet another Instant Update powered website', '', 'text', 'general', 'Website title', 'Title of your web site. Used in page titles and in page header.'),
(10, 'default_time_zone', 'Europe/Belgrade', 'Africa/Abidjan|Africa/Accra|Africa/Addis_Ababa|Africa/Algiers|Africa/Asmara|Africa/Asmera|Africa/Bamako|Africa/Bangui|Africa/Banjul|Africa/Bissau|Africa/Blantyre|Africa/Brazzaville|Africa/Bujumbura|Africa/Cairo|Africa/Casablanca|Africa/Ceuta|Africa/Conakry|Africa/Dakar|Africa/Dar_es_Salaam|Africa/Djibouti|Africa/Douala|Africa/El_Aaiun|Africa/Freetown|Africa/Gaborone|Africa/Harare|Africa/Johannesburg|Africa/Kampala|Africa/Khartoum|Africa/Kigali|Africa/Kinshasa|Africa/Lagos|Africa/Libreville|Africa/Lome|Africa/Luanda|Africa/Lubumbashi|Africa/Lusaka|Africa/Malabo|Africa/Maputo|Africa/Maseru|Africa/Mbabane|Africa/Mogadishu|Africa/Monrovia|Africa/Nairobi|Africa/Ndjamena|Africa/Niamey|Africa/Nouakchott|Africa/Ouagadougou|Africa/Porto-Novo|Africa/Sao_Tome|Africa/Timbuktu|Africa/Tripoli|Africa/Tunis|Africa/Windhoek|America/Adak|America/Anchorage|America/Anguilla|America/Antigua|America/Araguaina|America/Argentina/Buenos_Aires|America/Argentina/Catamarca|America/Argentina/ComodRivadavia|America/Argentina/Cordoba|America/Argentina/Jujuy|America/Argentina/La_Rioja|America/Argentina/Mendoza|America/Argentina/Rio_Gallegos|America/Argentina/Salta|America/Argentina/San_Juan|America/Argentina/San_Luis|America/Argentina/Tucuman|America/Argentina/Ushuaia|America/Aruba|America/Asuncion|America/Atikokan|America/Atka|America/Bahia|America/Bahia_Banderas|America/Barbados|America/Belem|America/Belize|America/Blanc-Sablon|America/Boa_Vista|America/Bogota|America/Boise|America/Buenos_Aires|America/Cambridge_Bay|America/Campo_Grande|America/Cancun|America/Caracas|America/Catamarca|America/Cayenne|America/Cayman|America/Chicago|America/Chihuahua|America/Coral_Harbour|America/Cordoba|America/Costa_Rica|America/Cuiaba|America/Curacao|America/Danmarkshavn|America/Dawson|America/Dawson_Creek|America/Denver|America/Detroit|America/Dominica|America/Edmonton|America/Eirunepe|America/El_Salvador|America/Ensenada|America/Fort_Wayne|America/Fortaleza|America/Glace_Bay|America/Godthab|America/Goose_Bay|America/Grand_Turk|America/Grenada|America/Guadeloupe|America/Guatemala|America/Guayaquil|America/Guyana|America/Halifax|America/Havana|America/Hermosillo|America/Indiana/Indianapolis|America/Indiana/Knox|America/Indiana/Marengo|America/Indiana/Petersburg|America/Indiana/Tell_City|America/Indiana/Vevay|America/Indiana/Vincennes|America/Indiana/Winamac|America/Indianapolis|America/Inuvik|America/Iqaluit|America/Jamaica|America/Jujuy|America/Juneau|America/Kentucky/Louisville|America/Kentucky/Monticello|America/Knox_IN|America/Kralendijk|America/La_Paz|America/Lima|America/Los_Angeles|America/Louisville|America/Lower_Princes|America/Maceio|America/Managua|America/Manaus|America/Marigot|America/Martinique|America/Matamoros|America/Mazatlan|America/Mendoza|America/Menominee|America/Merida|America/Metlakatla|America/Mexico_City|America/Miquelon|America/Moncton|America/Monterrey|America/Montevideo|America/Montreal|America/Montserrat|America/Nassau|America/New_York|America/Nipigon|America/Nome|America/Noronha|America/North_Dakota/Beulah|America/North_Dakota/Center|America/North_Dakota/New_Salem|America/Ojinaga|America/Panama|America/Pangnirtung|America/Paramaribo|America/Phoenix|America/Port-au-Prince|America/Port_of_Spain|America/Porto_Acre|America/Porto_Velho|America/Puerto_Rico|America/Rainy_River|America/Rankin_Inlet|America/Recife|America/Regina|America/Resolute|America/Rio_Branco|America/Rosario|America/Santa_Isabel|America/Santarem|America/Santiago|America/Santo_Domingo|America/Sao_Paulo|America/Scoresbysund|America/Shiprock|America/Sitka|America/St_Barthelemy|America/St_Johns|America/St_Kitts|America/St_Lucia|America/St_Thomas|America/St_Vincent|America/Swift_Current|America/Tegucigalpa|America/Thule|America/Thunder_Bay|America/Tijuana|America/Toronto|America/Tortola|America/Vancouver|America/Virgin|America/Whitehorse|America/Winnipeg|America/Yakutat|America/Yellowknife|Antarctica/Casey|Antarctica/Davis|Antarctica/DumontDUrville|Antarctica/Macquarie|Antarctica/Mawson|Antarctica/McMurdo|Antarctica/Palmer|Antarctica/Rothera|Antarctica/South_Pole|Antarctica/Syowa|Antarctica/Vostok|Arctic/Longyearbyen|Asia/Aden|Asia/Almaty|Asia/Amman|Asia/Anadyr|Asia/Aqtau|Asia/Aqtobe|Asia/Ashgabat|Asia/Ashkhabad|Asia/Baghdad|Asia/Bahrain|Asia/Baku|Asia/Bangkok|Asia/Beirut|Asia/Bishkek|Asia/Brunei|Asia/Calcutta|Asia/Choibalsan|Asia/Chongqing|Asia/Chungking|Asia/Colombo|Asia/Dacca|Asia/Damascus|Asia/Dhaka|Asia/Dili|Asia/Dubai|Asia/Dushanbe|Asia/Gaza|Asia/Harbin|Asia/Ho_Chi_Minh|Asia/Hong_Kong|Asia/Hovd|Asia/Irkutsk|Asia/Istanbul|Asia/Jakarta|Asia/Jayapura|Asia/Jerusalem|Asia/Kabul|Asia/Kamchatka|Asia/Karachi|Asia/Kashgar|Asia/Kathmandu|Asia/Katmandu|Asia/Kolkata|Asia/Krasnoyarsk|Asia/Kuala_Lumpur|Asia/Kuching|Asia/Kuwait|Asia/Macao|Asia/Macau|Asia/Magadan|Asia/Makassar|Asia/Manila|Asia/Muscat|Asia/Nicosia|Asia/Novokuznetsk|Asia/Novosibirsk|Asia/Omsk|Asia/Oral|Asia/Phnom_Penh|Asia/Pontianak|Asia/Pyongyang|Asia/Qatar|Asia/Qyzylorda|Asia/Rangoon|Asia/Riyadh|Asia/Saigon|Asia/Sakhalin|Asia/Samarkand|Asia/Seoul|Asia/Shanghai|Asia/Singapore|Asia/Taipei|Asia/Tashkent|Asia/Tbilisi|Asia/Tehran|Asia/Tel_Aviv|Asia/Thimbu|Asia/Thimphu|Asia/Tokyo|Asia/Ujung_Pandang|Asia/Ulaanbaatar|Asia/Ulan_Bator|Asia/Urumqi|Asia/Vientiane|Asia/Vladivostok|Asia/Yakutsk|Asia/Yekaterinburg|Asia/Yerevan|Atlantic/Azores|Atlantic/Bermuda|Atlantic/Canary|Atlantic/Cape_Verde|Atlantic/Faeroe|Atlantic/Faroe|Atlantic/Jan_Mayen|Atlantic/Madeira|Atlantic/Reykjavik|Atlantic/South_Georgia|Atlantic/St_Helena|Atlantic/Stanley|Australia/ACT|Australia/Adelaide|Australia/Brisbane|Australia/Broken_Hill|Australia/Canberra|Australia/Currie|Australia/Darwin|Australia/Eucla|Australia/Hobart|Australia/LHI|Australia/Lindeman|Australia/Lord_Howe|Australia/Melbourne|Australia/North|Australia/NSW|Australia/Perth|Australia/Queensland|Australia/South|Australia/Sydney|Australia/Tasmania|Australia/Victoria|Australia/West|Australia/Yancowinna|Europe/Amsterdam|Europe/Andorra|Europe/Athens|Europe/Belfast|Europe/Belgrade|Europe/Berlin|Europe/Bratislava|Europe/Brussels|Europe/Bucharest|Europe/Budapest|Europe/Chisinau|Europe/Copenhagen|Europe/Dublin|Europe/Gibraltar|Europe/Guernsey|Europe/Helsinki|Europe/Isle_of_Man|Europe/Istanbul|Europe/Jersey|Europe/Kaliningrad|Europe/Kiev|Europe/Lisbon|Europe/Ljubljana|Europe/London|Europe/Luxembourg|Europe/Madrid|Europe/Malta|Europe/Mariehamn|Europe/Minsk|Europe/Monaco|Europe/Moscow|Europe/Nicosia|Europe/Oslo|Europe/Paris|Europe/Podgorica|Europe/Prague|Europe/Riga|Europe/Rome|Europe/Samara|Europe/San_Marino|Europe/Sarajevo|Europe/Simferopol|Europe/Skopje|Europe/Sofia|Europe/Stockholm|Europe/Tallinn|Europe/Tirane|Europe/Tiraspol|Europe/Uzhgorod|Europe/Vaduz|Europe/Vatican|Europe/Vienna|Europe/Vilnius|Europe/Volgograd|Europe/Warsaw|Europe/Zagreb|Europe/Zaporozhye|Europe/Zurich|Indian/Antananarivo|Indian/Chagos|Indian/Christmas|Indian/Cocos|Indian/Comoro|Indian/Kerguelen|Indian/Mahe|Indian/Maldives|Indian/Mauritius|Indian/Mayotte|Indian/Reunion|Pacific/Apia|Pacific/Auckland|Pacific/Chatham|Pacific/Chuuk|Pacific/Easter|Pacific/Efate|Pacific/Enderbury|Pacific/Fakaofo|Pacific/Fiji|Pacific/Funafuti|Pacific/Galapagos|Pacific/Gambier|Pacific/Guadalcanal|Pacific/Guam|Pacific/Honolulu|Pacific/Johnston|Pacific/Kiritimati|Pacific/Kosrae|Pacific/Kwajalein|Pacific/Majuro|Pacific/Marquesas|Pacific/Midway|Pacific/Nauru|Pacific/Niue|Pacific/Norfolk|Pacific/Noumea|Pacific/Pago_Pago|Pacific/Palau|Pacific/Pitcairn|Pacific/Pohnpei|Pacific/Ponape|Pacific/Port_Moresby|Pacific/Rarotonga|Pacific/Saipan|Pacific/Samoa|Pacific/Tahiti|Pacific/Tarawa|Pacific/Tongatapu|Pacific/Truk|Pacific/Wake|Pacific/Wallis|Pacific/Yap|Brazil/Acre|Brazil/DeNoronha|Brazil/East|Brazil/West|Canada/Atlantic|Canada/Central|Canada/East-Saskatchewan|Canada/Eastern|Canada/Mountain|Canada/Newfoundland|Canada/Pacific|Canada/Saskatchewan|Canada/Yukon|CET|Chile/Continental|Chile/EasterIsland|CST6CDT|Cuba|EET|Egypt|Eire|EST|EST5EDT|Etc/GMT|Etc/GMT+0|Etc/GMT+1|Etc/GMT+10|Etc/GMT+11|Etc/GMT+12|Etc/GMT+2|Etc/GMT+3|Etc/GMT+4|Etc/GMT+5|Etc/GMT+6|Etc/GMT+7|Etc/GMT+8|Etc/GMT+9|Etc/GMT-0|Etc/GMT-1|Etc/GMT-10|Etc/GMT-11|Etc/GMT-12|Etc/GMT-13|Etc/GMT-14|Etc/GMT-2|Etc/GMT-3|Etc/GMT-4|Etc/GMT-5|Etc/GMT-6|Etc/GMT-7|Etc/GMT-8|Etc/GMT-9|Etc/GMT0|Etc/Greenwich|Etc/UCT|Etc/Universal|Etc/UTC|Etc/Zulu|Factory|GB|GB-Eire|GMT|GMT+0|GMT-0|GMT0|Greenwich|Hongkong|HST|Iceland|Iran|Israel|Jamaica|Japan|Kwajalein|Libya|MET|Mexico/BajaNorte|Mexico/BajaSur|Mexico/General|MST|MST7MDT|Navajo|NZ|NZ-CHAT|Poland|Portugal|PRC|PST8PDT|ROC|ROK|Singapore|Turkey|UCT|Universal|US/Alaska|US/Aleutian|US/Arizona|US/Central|US/East-Indiana|US/Eastern|US/Hawaii|US/Indiana-Starke|US/Michigan|US/Mountain|US/Pacific|US/Pacific-New|US/Samoa|UTC|W-SU|WET|Zulu', 'select', 'date', 'Time zone', 'Time zone for whole website'),
(7, 'license_key', '', NULL, 'text', 'hidden', NULL, NULL),
(42, 'sticky_toolbar', 'no', 'yes|no', 'select', 'hidden', 'Use sticky toolbar', 'Sticky toolbar will appear above the content being edited. Set to "no" to use floating one, if you have problems with sticky toolbar.'),
(36, 'assets_folder', 'iu-assets', NULL, 'text', 'general', 'Assets path', 'Relative path (from your website root) to the assets folder.'),
(18, 'custom_footer_text', '', NULL, 'text', 'branding', 'Footer text', 'Text to display in your website footer. You can use HTML code here.'),
(20, 'admin_email', '', NULL, 'text', 'contacting', 'Admin e-mail', 'Administrator e-mail address. All system e-mail messages will be sent to this e-mail address.'),
(28, 'date_format', 'F j, Y', NULL, 'text', 'date', 'Date format', 'Date representation format, as specified by PHP. See: http://goo.gl/Ch25n'),
(50, 'custom_footer_text2', '', NULL, 'text', 'branding', 'Footer text #2', 'Second line of the footer text.'),
(32, 'google_analytics_id', '', NULL, 'text', 'general', 'Google Analytics ID', 'Enter your Google Analytics Web ID and your web site will be automatically tracked using GA. Format of this input is UA-123456-7. Leave empty to disable tracking.'),
(43, 'menu_position', 'right', 'left|right', 'select', 'frontend', 'Main menu position', 'Position of the main Instant Update front-end menu.'),
(44, 'append_sitename_titles', 'no', 'yes|no', 'select', 'frontend', 'Append site title', 'Append site title to all pages.'),
(45, 'comments_enabled', 'no', 'no|Facebook|Disqus', 'select', 'frontend', 'Enable comments', 'Allow website visitors to leave comments on your website. You can use either Facebook or Disqus for commenting engine.'),
(46, 'comments_engine_id', '', NULL, 'text', 'frontend', 'Comments engine ID', 'Your Facebook application ID or your Disqus identifier.'),
(49, 'datetime_format', 'F j, Y @ H:i', NULL, 'text', 'date', 'Date/time format', 'Date and time representation format, as specified by PHP. See: http://goo.gl/Ch25n'),
(51, 'last_ping', '0', NULL, 'text', 'hidden', 'Last ping', 'Last ping timestamp'),
(48, 'datepicker_format', 'dd/mm/yy', 'yy/mm/dd|dd/mm/yy|mm/dd/yy', 'select', 'date', 'Date picker format', 'Choose date picker format which suits your needs.');

--
-- Table structure for table `iu4_userroles`
--

DROP TABLE IF EXISTS `%prefix%userroles`;
CREATE TABLE IF NOT EXISTS `%prefix%userroles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `access_level` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `iu4_userroles`
--

INSERT INTO `%prefix%userroles` (`id`, `name`, `access_level`) VALUES
(1, 'Administrator', 100),
(4, 'User', 0),
(3, 'Editor', 50);

--
-- Table structure for table `iu4_users`
--

DROP TABLE IF EXISTS `%prefix%users`;
CREATE TABLE IF NOT EXISTS `%prefix%users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `userrole_id` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user.jpg',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;









--
-- 4.1
--


INSERT INTO `%prefix%contenttypes` (`id` ,`name` ,`description` ,`classname`) VALUES (NULL , 'Gallery', 'Image gallery', 'Gallery') ;

DROP TABLE IF EXISTS `%prefix%galleryitems`;

CREATE TABLE IF NOT EXISTS `%prefix%galleryitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

DROP TABLE IF EXISTS `%prefix%images`;

INSERT INTO `%prefix%settings` (`id`, `name`, `value`, `options`, `type`, `group`, `label`, `description`) VALUES (NULL, 'cache_duration', '0', NULL, 'text', 'general', 'Cache duration (mins)', 'Page cache duration for logged out users. Leave 0 to disable. If using PHP code in your templates it is advised not to use caching globally (leave 0) or by page.');

ALTER TABLE `%prefix%pages` DROP `default` ;
ALTER TABLE `%prefix%pages` ADD `custom_caching` TINYINT( 1 ) NOT NULL AFTER `editor_id` , ADD `custom_caching_duration` INT NOT NULL AFTER `custom_caching`;

-- indexes

ALTER TABLE `%prefix%contentrevisions` ADD INDEX `content_id` ( `content_id` ) ;
ALTER TABLE `%prefix%contentrevisions` ADD INDEX `user_id` ( `user_id` ) ;

ALTER TABLE `%prefix%contents` ADD INDEX `div` ( `div` ) ;
ALTER TABLE `%prefix%contents` ADD INDEX `page_id` ( `page_id` ) ;
ALTER TABLE `%prefix%contents` ADD INDEX `contenttype_id` ( `contenttype_id` ) ;
ALTER TABLE `%prefix%contents` ADD INDEX `is_global` ( `is_global` ) ;
ALTER TABLE `%prefix%contents` ADD INDEX `editor_id` ( `editor_id` ) ;

ALTER TABLE `%prefix%contents_users` ADD INDEX `content_id` ( `content_id` ) ;
ALTER TABLE `%prefix%contents_users` ADD INDEX `user_id` ( `user_id` ) ;

ALTER TABLE `%prefix%contenttypes` ADD INDEX `classname` ( `classname` ) ;
	
ALTER TABLE `%prefix%filerevisions` ADD INDEX `file_id` ( `file_id` ) ;
ALTER TABLE `%prefix%filerevisions` ADD INDEX `user_id` ( `user_id` ) ;
ALTER TABLE `%prefix%filerevisions` ADD INDEX `created` ( `created` ) ;

ALTER TABLE `%prefix%files` ADD INDEX `editor_id` ( `editor_id` ) ;
ALTER TABLE `%prefix%files` ADD INDEX `updated` ( `updated` ) ;

ALTER TABLE `%prefix%galleryitems` ADD INDEX `order` ( `order` ) ;
ALTER TABLE `%prefix%galleryitems` ADD INDEX `user_id` ( `user_id` ) ;
ALTER TABLE `%prefix%galleryitems` ADD INDEX `content_id` ( `content_id` ) ;

ALTER TABLE `%prefix%hits` ADD INDEX `returning` ( `returning` ) ;
ALTER TABLE `%prefix%hits` ADD INDEX `page_id` ( `page_id` ) ;

ALTER TABLE `%prefix%languages` ADD INDEX `slug` ( `slug` ) ;
ALTER TABLE `%prefix%languages` ADD INDEX `active` ( `active` ) ;

ALTER TABLE `%prefix%logs` ADD INDEX `user_id` ( `user_id` ) ;
ALTER TABLE `%prefix%logs` ADD INDEX `ip_address` ( `ip_address` ) ;

ALTER TABLE `%prefix%pages` ADD INDEX `file_id` ( `file_id` ) ;
ALTER TABLE `%prefix%pages` ADD INDEX `user_id` ( `user_id` ) ;
ALTER TABLE `%prefix%pages` ADD INDEX `editor_id` ( `editor_id` ) ;

ALTER TABLE `%prefix%pages_users` ADD INDEX `user_id` ( `user_id` ) ;
ALTER TABLE `%prefix%pages_users` ADD INDEX `page_id` ( `page_id` ) ;

ALTER TABLE `%prefix%permissions` ADD INDEX `key` ( `key` ) ;

ALTER TABLE `%prefix%permissions_userroles` ADD INDEX `permission_id` ( `permission_id` ) ;
ALTER TABLE `%prefix%permissions_userroles` ADD INDEX `userrole_id` ( `userrole_id` ) ;

ALTER TABLE `%prefix%permissions_users` ADD INDEX `permission_id` ( `permission_id` ) ;
ALTER TABLE `%prefix%permissions_users` ADD INDEX `user_id` ( `user_id` ) ;

ALTER TABLE `%prefix%phrases` ADD INDEX `filter` ( `filter` ) ;
ALTER TABLE `%prefix%phrases` ADD INDEX `phrase` ( `phrase` ) ;

ALTER TABLE `%prefix%repeatableitems` ADD INDEX `user_id` ( `user_id` ) ;
ALTER TABLE `%prefix%repeatableitems` ADD INDEX `content_id` ( `content_id` ) ;

ALTER TABLE `%prefix%settings` ADD UNIQUE `name` ( `name` ) ;
ALTER TABLE `%prefix%settings` ADD INDEX `group` ( `group` ) ;

ALTER TABLE `%prefix%users` ADD UNIQUE `email` ( `email` ) ;
ALTER TABLE `%prefix%users` ADD INDEX `userrole_id` ( `userrole_id` ) ;
ALTER TABLE `%prefix%users` ADD INDEX `active` ( `active` ) ;


--
-- 4.2
--


ALTER TABLE `%prefix%users` CHANGE `password` `password` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `name` `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;

INSERT INTO `%prefix%settings` (`id` ,`name` ,`value` ,`options` ,`type` ,`group` ,`label` ,`description`) VALUES (NULL , 'use_tidy', 'yes', 'yes|no', 'select', 'general', 'Clean HTML', 'Use HTML Tidy to cleanup and format your website HTML source code output. May cause problems with some HTML5 tags.');

 
 
 