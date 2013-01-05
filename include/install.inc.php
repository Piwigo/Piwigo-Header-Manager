<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
  
function header_manager_install() 
{
  global $conf, $prefixeTable;

  // configuration
  if (empty($conf['header_manager']))
  {
    $header_manager_default_config = serialize(array(
      'width' => 1000,
      'height' => 150,
      'image' => 'random',
      'display' => 'image_only',
      'banner_on_picture' => true,
      ));
    
    conf_update_param('header_manager', $header_manager_default_config);
    $conf['header_manager'] = $header_manager_default_config;
  }
  else
  {
    $new_conf = is_string($conf['header_manager']) ? unserialize($conf['header_manager']) : $conf['header_manager'];
    if (!isset($new_conf['banner_on_picture']))
    {
      $new_conf['banner_on_picture'] = true;
      $conf['header_manager'] = serialize($new_conf);
      conf_update_param('header_manager', $conf['header_manager']);
    }
  }

  // banners directory
  if (!file_exists(PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'banners/')) 
  {
    mkdir(PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'banners/', 0755);
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
}

?>