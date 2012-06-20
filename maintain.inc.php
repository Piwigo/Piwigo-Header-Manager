<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('HEADER_MANAGER_DIR', PWG_LOCAL_DIR . 'banners/');

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
  conf_update_param('header_manager', header_manager_default_config);
  mkdir(HEADER_MANAGER_DIR, 0755);
}

function plugin_activate()
{
  global $conf;

  if (empty($conf['header_manager']))
  {
    conf_update_param('header_manager', header_manager_default_config);
  }
  if (!file_exists(HEADER_MANAGER_DIR)) 
  {
    mkdir(HEADER_MANAGER_DIR, 0755);
  }
}

function plugin_uninstall() 
{
  pwg_query('DELETE FROM `'. CONFIG_TABLE .'` WHERE param = "header_manager" LIMIT 1;');
}

?>