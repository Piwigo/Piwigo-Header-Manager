<?php
defined('HEADER_MANAGER_PATH') or die('Hacking attempt!');

global $template, $page;
load_language('plugin.lang', HEADER_MANAGER_PATH);

$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : 'config';

if ($page['tab'] != 'album')
{
  // tabsheet
  include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');    
  $tabsheet = new tabsheet();
  $tabsheet->set_id('header_manager');
  $tabsheet->add('config', l10n('Configuration'), HEADER_MANAGER_ADMIN . '-config');
  $tabsheet->add('add', l10n('Add a banner'), HEADER_MANAGER_ADMIN . '-add');
  $tabsheet->select($page['tab']);
  $tabsheet->assign();
}

// template
$template->assign(array(
  'CONFIG_URL' => HEADER_MANAGER_ADMIN . '-config',
  'ADD_IMAGE_URL' => HEADER_MANAGER_ADMIN . '-add',
  ));

// include page
include(HEADER_MANAGER_PATH . 'admin/' . $page['tab'] . '.php');

$template->assign('HEADER_MANAGER_PATH', HEADER_MANAGER_PATH);

$template->assign_var_from_handle('ADMIN_CONTENT', 'header_manager');
