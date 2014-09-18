<?php
defined('HEADER_MANAGER_PATH') or die('Hacking attempt!');


// save config
if (isset($_POST['save_config']))
{
  if ($_POST['display'] == 'with_text')
  {
    conf_update_param('page_banner', $_POST['conf_page_banner'], true);
  }
  
  $conf['header_manager'] = array(
    'width' => $conf['header_manager']['width'],
    'height' => $conf['header_manager']['height'],
    'image' => @$_POST['image'],
    'display' => $_POST['display'],
    'banner_on_picture' => isset($_POST['banner_on_picture']),
    );
  conf_update_param('header_manager', $conf['header_manager']);
  
  $page['infos'][] = l10n('Information data registered in database');
}


// delete banner
if (isset($_GET['delete_banner']))
{
  $banner = get_banner($_GET['delete_banner']);
  if ($banner !== false or @unlink($banner['PATH']))
  {
    @unlink($banner['THUMB']);
    
    if ($conf['header_manager']['image'] == $_GET['delete_banner'])
    {
      $conf['header_manager']['image'] = 'random';
      conf_update_param('header_manager', $conf['header_manager']);
    }
    
    $query = '
DELETE FROM '.HEADER_MANAGER_TABLE.'
  WHERE image = "'.$_GET['delete_banner'].'"
;';
    pwg_query($query);
    
    $page['infos'][] = l10n('Banner deleted');
  }
  else
  {
    $page['warnings'][] = l10n('File/directory read error').' : ' . HEADER_MANAGER_DIR . $_GET['delete_banner'];
  }
}

if (isset($_GET['delete_cat_banner']))
{
  $query = '
DELETE FROM '.HEADER_MANAGER_TABLE.'
  WHERE category_id = "'.$_GET['delete_cat_banner'].'"
;';
  pwg_query($query);
}


// album banners
$query = '
SELECT
    id,
    name,
    permalink,
    dir,
    image,
    deep
  FROM '.HEADER_MANAGER_TABLE.' AS h
    INNER JOIN '.CATEGORIES_TABLE.' AS c
    ON h.category_id = c.id
  ORDER BY global_rank ASC
;';
$categories = query2array($query, 'id');

foreach ($categories as $category)
{
  $tpl_cat =
    array(
      'NAME'     => get_cat_display_name_from_id($category['id'], HEADER_MANAGER_ADMIN.'-album&amp;cat_id='),
      'ID'       => $category['id'],
      'IMAGE'    => get_filename_wo_extension($category['image']),
      'DEEP'     => $category['deep'],
      'U_DELETE' => HEADER_MANAGER_ADMIN.'-config&amp;delete_cat_banner='.$category['id'],
    );
  
  $template->append('categories', $tpl_cat);
}


// config template
if (empty($conf['header_manager']['image']) or get_banner($conf['header_manager']['image']) === false)
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

$template->set_filename('header_manager', realpath(HEADER_MANAGER_PATH . 'admin/template/config.tpl'));
