<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
  
include_once(PHPWG_PLUGINS_PATH . 'header_manager/include/install.inc.php');

function plugin_install() 
{
  header_manager_install();
  define('header_manager_installed', true);
}

function plugin_activate()
{
  if (!defined('header_manager_installed'))
  {
    header_manager_install()
  }
}

function plugin_uninstall() 
{
  global $prefixeTable;
  
  pwg_query('DROP TABLE `' .$prefixeTable . 'category_banner`;');
  pwg_query('DELETE FROM `'. CONFIG_TABLE .'` WHERE param = "header_manager" LIMIT 1;');
}

?>