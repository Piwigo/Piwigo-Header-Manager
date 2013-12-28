<?php 
/*
Plugin Name: Header Manager
Version: auto
Description: Header Manager allows to simply manage gallery banners. You can upload a picture from your computer or use a picture already in the gallery.
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $prefixeTable;
define('HEADER_MANAGER_PATH',    PHPWG_PLUGINS_PATH . 'header_manager/');
define('HEADER_MANAGER_ADMIN',   get_root_url() . 'admin.php?page=plugin-header_manager');
define('HEADER_MANAGER_DIR',     PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'banners/');
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
 * initialization
 */
function header_manager_init()
{
  global $conf, $pwg_loaded_plugins, $page;
  
  include_once(HEADER_MANAGER_PATH . 'maintain.inc.php');
  $maintain = new header_manager_maintain('header_manager');
  $maintain->autoUpdate(HEADER_MANAGER_VERSION, 'install');
  
  $conf['header_manager'] = unserialize($conf['header_manager']);
}

/**
 * Header Manager admin link
 */
function header_manager_admin_menu($menu) 
{
  $menu[] = array(
    'NAME' => 'Header Manager',
    'URL' => HEADER_MANAGER_ADMIN,
    );
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

/**
 * clean table when categories are deleted
 */
function header_manager_delete_categories($ids)
{
  $query = '
DELETE FROM '.HEADER_MANAGER_TABLE.'
  WHERE category_id IN('.implode(',', $ids).')
;';
  pwg_query($query);
}
