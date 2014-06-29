<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class header_manager_maintain extends PluginMaintain
{
  private $default_conf = array(
      'width' => 1000,
      'height' => 150,
      'image' => 'random',
      'display' => 'image_only',
      'banner_on_picture' => true,
      'keep_ratio' => true
    );
    
  private $table;
  
  function __construct($plugin_id)
  {
    global $prefixeTable;
    
    parent::__construct($plugin_id);
    $this->table = $prefixeTable . 'category_banner';
  }

  function install($plugin_version, &$errors=array())
  {
    global $conf, $prefixeTable;

    // configuration
    if (empty($conf['header_manager']))
    {
      conf_update_param('header_manager', $this->default_conf, true);
    }
    else
    {
      $new_conf = safe_unserialize($conf['header_manager']);
      
      if (!isset($new_conf['banner_on_picture']))
      {
        $new_conf['banner_on_picture'] = true;
      }
      else if (!isset($new_conf['keep_ratio']))
      {
        $new_conf['keep_ratio'] = true;
      }
      
      conf_update_param('header_manager', $new_conf, true);
    }

    // banners directory
    if (!file_exists(PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'banners')) 
    {
      mkdir(PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'banners', 0755);
    }

    // banners table
    $query = '
CREATE TABLE IF NOT EXISTS `' .$this->table . '` (
  `category_id` smallint(5) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `deep` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;';
    pwg_query($query);
  }

  function update($old_version, $new_version, &$errors=array())
  {
    $this->install($new_version, $errors);
  }

  function uninstall()
  {
    conf_delete_param('header_manager');

    pwg_query('DROP TABLE `' .$this->table . '`;');
  }
}
