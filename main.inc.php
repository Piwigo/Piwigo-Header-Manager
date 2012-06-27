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

define('HEADER_MANAGER_PATH', PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');
define('HEADER_MANAGER_ADMIN', get_root_url() . 'admin.php?page=plugin-' . basename(dirname(__FILE__)));
define('HEADER_MANAGER_DIR', PWG_LOCAL_DIR . 'banners/');

add_event_handler('init', 'header_manager_init');

function header_manager_init()
{
  if (defined('PWG_HELP')) return;
  
  global $conf;
  $conf['header_manager'] = unserialize($conf['header_manager']);
    
  include(HEADER_MANAGER_PATH . 'include/functions.inc.php');
  include(HEADER_MANAGER_PATH . 'include/header_manager.inc.php');
  
  add_event_handler('render_page_banner', 'header_manager_render');

  if (defined('IN_ADMIN'))
  {
    add_event_handler('get_admin_plugin_menu_links', 'header_manager_admin_menu');
  }
}

?>