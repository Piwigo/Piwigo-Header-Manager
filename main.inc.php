<?php 
/*
Plugin Name: Header Manager
Version: auto
Description: Header Manager allows to simply manage gallery banners. You can upload a picture from your computer or use a picture already in the gallery.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=608
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable;
define('HEADER_MANAGER_PATH',    PHPWG_PLUGINS_PATH . 'header_manager/');
define('HEADER_MANAGER_ADMIN',   get_root_url() . 'admin.php?page=plugin-header_manager');
define('HEADER_MANAGER_DIR',     PWG_LOCAL_DIR . 'banners/');
define('HEADER_MANAGER_TABLE',   $prefixeTable . 'category_banner');
define('HEADER_MANAGER_VERSION', 'auto');


add_event_handler('init', 'header_manager_init');
  
if (defined('IN_ADMIN'))
{
  add_event_handler('get_admin_plugin_menu_links', 'header_manager_admin_menu');
  add_event_handler('tabsheet_before_select', 'header_manager_tab', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
  add_event_handler('delete_categories', 'header_manager_delete_categories');
}
else if (!defined('PWG_HELP'))
{
  add_event_handler('render_page_banner', 'header_manager_render');
}

include_once(HEADER_MANAGER_PATH . 'include/functions.inc.php');
include_once(HEADER_MANAGER_PATH . 'include/header_manager.inc.php');


/**
 * update plugin & unserialize conf
 */
function header_manager_init()
{
  global $conf, $pwg_loaded_plugins, $page;
  
  if (
    $pwg_loaded_plugins['header_manager']['version'] == 'auto' or
    version_compare($pwg_loaded_plugins['header_manager']['version'], HEADER_MANAGER_VERSION, '<')
  )
  {
    include_once(HEADER_MANAGER_PATH . 'include/install.inc.php');
    header_manager_install();
    
    if ($pwg_loaded_plugins['header_manager']['version'] != 'auto')
    {
      $query = '
UPDATE '. PLUGINS_TABLE .'
SET version = "'. HEADER_MANAGER_VERSION .'"
WHERE id = "header_manager"';
      pwg_query($query);
      
      $pwg_loaded_plugins['header_manager']['version'] = HEADER_MANAGER_VERSION;
      
      if (defined('IN_ADMIN'))
      {
        $_SESSION['page_infos'][] = 'Header Manager updated to version '. HEADER_MANAGER_VERSION;
      }
    }
  }
  
  $conf['header_manager'] = unserialize($conf['header_manager']);
}

/**
 * Header Manager admin link
 */
function header_manager_admin_menu($menu) 
{
  array_push($menu, array(
    'NAME' => 'Header Manager',
    'URL' => HEADER_MANAGER_ADMIN,
  ));
  return $menu;
}

/**
 * tab on album edition page
 */
function header_manager_tab($sheets, $id)
{
  if ($id == 'album')
  {
    load_language('plugin.lang', HEADER_MANAGER_PATH);
    
    $sheets['headermanager'] = array(
      'caption' => l10n('Banner'),
      'url' => HEADER_MANAGER_ADMIN.'-album&amp;cat_id='.$_GET['cat_id'],
      );
  }
  
  return $sheets;
}

?>