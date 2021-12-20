<?php 
/*
Plugin Name: Header Manager
Version: auto
Description: Header Manager allows to simply manage gallery banners. You can upload a picture from your computer or use a picture already in the gallery.
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
Has Settings: true
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'header_manager')
{
  add_event_handler('init', 'header_manager_error');
  function header_manager_error()
  {
    global $page;
    $page['errors'][] = 'Header Manager folder name is incorrect, uninstall the plugin and rename it to "header_manager"';
  }
  return;
}

global $prefixeTable, $conf;

define('HEADER_MANAGER_PATH',    PHPWG_PLUGINS_PATH . 'header_manager/');
define('HEADER_MANAGER_ADMIN',   get_root_url() . 'admin.php?page=plugin-header_manager');
define('HEADER_MANAGER_DIR',     PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'banners/');
define('HEADER_MANAGER_TABLE',   $prefixeTable . 'category_banner');

include_once(HEADER_MANAGER_PATH . 'include/functions.inc.php');
include_once(HEADER_MANAGER_PATH . 'include/header_manager.inc.php');


$conf['header_manager'] = safe_unserialize($conf['header_manager']);

  
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
