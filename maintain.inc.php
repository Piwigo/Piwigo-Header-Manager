<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class header_manager_maintain extends PluginMaintain
{
  private $installed = false;
  
  private $default_conf = array(
      'width' => 1000,
      'height' => 150,
      'image' => 'random',
      'display' => 'image_only',
      'banner_on_picture' => true,
    );

  function install($plugin_version, &$errors=array())
  {
    global $conf, $prefixeTable;

    // configuration
    if (empty($conf['header_manager']))
    {
      $conf['header_manager'] = serialize($this->default_conf);
      conf_update_param('header_manager', $conf['header_manager']);
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
    if (!file_exists(PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'banners')) 
    {
      mkdir(PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'banners', 0755);
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

    $this->installed = true;
  }

  function activate($plugin_version, &$errors=array())
  {
    if (!$this->installed)
    {
      $this->install($plugin_version, $errors);
    }
  }

  function deactivate()
  {
  }

  function uninstall()
  {
    global $prefixeTable;

    conf_delete_param('header_manager');

    pwg_query('DROP TABLE `' .$prefixeTable . 'category_banner`;');
  }
}
