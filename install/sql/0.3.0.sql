DROP TABLE IF EXISTS `%prefix%plugins`;
CREATE TABLE IF NOT EXISTS `%prefix%plugins` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `author_url` varchar(255) DEFAULT NULL,
  `plugin_url` varchar(255) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `data` mediumtext
) ENGINE=MyISAM;

ALTER TABLE `%prefix%plugins`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `%prefix%plugins`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;