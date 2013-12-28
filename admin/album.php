<?php
defined('HEADER_MANAGER_PATH') or die ("Hacking attempt!");

// +-----------------------------------------------------------------------+
// | Basic checks                                                          |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('cat_id', $_GET, false, PATTERN_ID, true);

$admin_album_base_url = get_root_url().'admin.php?page=album-'.$_GET['cat_id'];
$self_url = HEADER_MANAGER_ADMIN.'-album&amp;cat_id='.$_GET['cat_id'];

$query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$_GET['cat_id'].'
;';
$category = pwg_db_fetch_assoc(pwg_query($query));

if (!isset($category['id']))
{
  die("unknown album");
}

$cat_id = $_GET['cat_id'];


// +-----------------------------------------------------------------------+
// | Tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('album');
$tabsheet->select('headermanager');
$tabsheet->assign();

$page['active_menu'] = get_active_menu('album');


// +-----------------------------------------------------------------------+
// | Save Form                                                             |
// +-----------------------------------------------------------------------+
if (isset($_POST['save_banner']))
{
  if (!isset($_POST['image']) or $_POST['image'] == 'default')
  {
    $query = '
DELETE FROM '.HEADER_MANAGER_TABLE.'
  WHERE category_id = '.$cat_id.'
;';
    pwg_query($query);
  }
  else
  {
    $query = '
INSERT INTO '.HEADER_MANAGER_TABLE.'(
    category_id,
    image,
    deep
  )
  VALUES (
    '.$cat_id.',
    "'.$_POST['image'].'",
    '.(int)isset($_POST['deep']).'
  )
  ON DUPLICATE KEY UPDATE
    image = "'.$_POST['image'].'",
    deep = '.(int)isset($_POST['deep']).'
;';
    pwg_query($query);
  }
}


// +-----------------------------------------------------------------------+
// | Display page                                                          |
// +-----------------------------------------------------------------------+
$query = '
SELECT *
  FROM '.HEADER_MANAGER_TABLE.'
  WHERE category_id = '.$cat_id.'
;';
$result = pwg_query($query);

if (pwg_db_num_rows($result))
{
  $cat_banner = pwg_db_fetch_assoc($result);
  $banner = get_banner($cat_banner['image']);
  if ($banner === false)
  {
    $cat_banner['image'] = 'default';
  }
}
else
{
  $cat_banner = array(
    'image' => 'default',
    'deep' => 1,
    );
}

$template->assign(array(
  'banners' => list_banners(true),
  'BANNER_IMAGE' => $cat_banner['image'],
  'BANNER_DEEP' => $cat_banner['deep'],
  'F_ACTION' => $self_url,
  'CATEGORIES_NAV' => get_cat_display_name_cache(
    $category['uppercats'],
    HEADER_MANAGER_ADMIN.'-album&amp;cat_id='
    ),
));

$template->set_filename('header_manager', realpath(HEADER_MANAGER_PATH . 'admin/template/album.tpl'));
