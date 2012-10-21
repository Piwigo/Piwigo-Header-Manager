<?php
if (!defined('HEADER_MANAGER_PATH')) die('Hacking attempt!');

// change banner to last uploaded
if ( pwg_get_session_var('added_banner')!==null and $conf['header_manager']['image']!='random' )
{
  $conf['header_manager']['image'] = pwg_get_session_var('added_banner');
  conf_update_param('header_manager', serialize($conf['header_manager']));
  pwg_unset_session_var('added_banner');
}

// save config
if (isset($_POST['save_config']))
{
  if ($_POST['display'] == 'with_text')
  {
    $conf['page_banner'] = $_POST['conf_page_banner'];
    conf_update_param('page_banner', $conf['page_banner']);
  }
  
  $conf['header_manager'] = array(
    'width' => $conf['header_manager']['width'],
    'height' => $conf['header_manager']['height'],
    'image' => @$_POST['image'],
    'display' => $_POST['display'],
    'banner_on_picture' => isset($_POST['banner_on_picture']),
    );
  conf_update_param('header_manager', serialize($conf['header_manager']));
  
  array_push($page['infos'], l10n('Information data registered in database'));
}

// delete banner
if (isset($_GET['delete_banner']))
{
  $banner = get_banner($_GET['delete_banner']);
  if ( $banner !== false or @unlink($banner['PATH']) )
  {
    @unlink($banner['THUMB']);
    
    if ($conf['header_manager']['image'] == $_GET['delete_banner'])
    {
      $conf['header_manager']['image'] = 'random';
      conf_update_param('header_manager', serialize($conf['header_manager']));
    }
    
    $query = '
DELETE FROM '.HEADER_MANAGER_TABLE.'
  WHERE image = "'.$_GET['delete_banner'].'"
;';
    pwg_query($query);
    
    array_push($page['infos'], l10n('Banner deleted'));
  }
  else
  {
    array_push($page['warnings'], l10n('File/directory read error').' : ' . HEADER_MANAGER_DIR . $_GET['delete_banner']);
  }
}

// config template
if ( empty($conf['header_manager']['image']) or get_banner($conf['header_manager']['image']) === false )
{
  $conf['header_manager']['image'] = 'random';
}

$template->assign(array(
  'banners' => list_banners(true),
  'CONF_PAGE_BANNER' => stripslashes(htmlspecialchars($conf['page_banner'])),
  'BANNER_IMAGE' => $conf['header_manager']['image'],
  'BANNER_DISPLAY' => $conf['header_manager']['display'],
  'BANNER_ON_PICTURE' => $conf['header_manager']['banner_on_picture']
  ));

$template->set_filename('header_manager', dirname(__FILE__).'/template/config.tpl');

?>