<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
  
function plugin_install() 
{
  global $conf, $prefixeTable;

  // configuration
  if (empty($conf['header_manager']))
  {
    $header_manager_default_config = serialize(array(
      'width' => 1000,
      'height' => 150,
      'image' => 'random',
      'display' => 'image_only'
      ));
    
    conf_update_param('header_manager', $header_manager_default_config);
    $conf['header_manager'] = $header_manager_default_config;
  }

  // banners directory
  if (!file_exists(PWG_LOCAL_DIR . 'banners/')) 
  {
    mkdir(PWG_LOCAL_DIR . 'banners/', 0755);
  }

  // banners table
  $query = '
CREATE TABLE IF NOT EXISTS `' .$prefixeTable . 'category_banner` (
  `category_id` smallint(5) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `deep` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;';
  pwg_query($query);
  
  define('header_manager_installed', true);
}

function plugin_activate()
{
  if (!defined('header_manager_installed'))
  {
    plugin_install();
  }
}

function plugin_uninstall() 
{
  global $prefixeTable;
  
  pwg_query('DROP TABLE `' .$prefixeTable . 'category_banner`;');
  pwg_query('DELETE FROM `'. CONFIG_TABLE .'` WHERE param = "header_manager" LIMIT 1;');
}

?>