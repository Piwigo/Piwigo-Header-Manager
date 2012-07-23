<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('header_dir', PWG_LOCAL_DIR . 'banners/');

define(
  'header_manager_default_config', 
  serialize(array(
    'width' => 1000,
    'height' => 150,
    'image' => 'random',
    'display' => 'image_only'
    ))
  );
  

function plugin_install() 
{
  global $prefixeTable;
  
	pwg_query(
'CREATE TABLE IF NOT EXISTS `' .$prefixeTable . 'category_banner` (
  `category_id` smallint(5) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `deep` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');

  conf_update_param('header_manager', header_manager_default_config);
  mkdir(header_dir, 0755);
}

function plugin_activate()
{
  global $conf, $prefixeTable;

  if (empty($conf['header_manager']))
  {
    conf_update_param('header_manager', header_manager_default_config);
  }
  if (!file_exists(header_dir)) 
  {
    mkdir(header_dir, 0755);
  }
  
  pwg_query(
'CREATE TABLE IF NOT EXISTS `' .$prefixeTable . 'category_banner` (
  `category_id` smallint(5) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `deep` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');
}

function plugin_uninstall() 
{
  global $prefixeTable;
  
  pwg_query('DROP TABLE `' .$prefixeTable . 'category_banner`;');
  pwg_query('DELETE FROM `'. CONFIG_TABLE .'` WHERE param = "header_manager" LIMIT 1;');
}

?>