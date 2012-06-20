<?php
if (!defined('HEADER_MANAGER_PATH')) die('Hacking attempt!');

global $template, $page;
load_language('plugin.lang', HEADER_MANAGER_PATH);

if (!file_exists(HEADER_MANAGER_DIR)) 
{
  mkdir(HEADER_MANAGER_DIR, 0755);
}


// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : $page['tab'] = 'config';
  
$tabsheet = new tabsheet();
$tabsheet->add('config', l10n('Configuration'), HEADER_MANAGER_ADMIN . '-config');
$tabsheet->add('add', l10n('Add a banner'), HEADER_MANAGER_ADMIN . '-add');
$tabsheet->select($page['tab']);
$tabsheet->assign();

// include page
include(HEADER_MANAGER_PATH . 'admin/' . $page['tab'] . '.php');

// template
$template->assign(array(
  'HEADER_MANAGER_PATH'=> HEADER_MANAGER_PATH,
  'CONFIG_URL' => HEADER_MANAGER_ADMIN . '-config',
  'ADD_IMAGE_URL' => HEADER_MANAGER_ADMIN . '-add',
  ));
$template->assign_var_from_handle('ADMIN_CONTENT', 'header_manager');

?>