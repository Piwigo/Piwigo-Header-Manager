<?php
if (!defined('HEADER_MANAGER_PATH')) die('Hacking attempt!');

// cancel crop
if (isset($_POST['cancel_crop']))
{
  $banner = get_banner($_POST['picture_file']);
  @unlink($banner['PATH']);
  @unlink($banner['THUMB']);
}
// apply crop and redirect
else if (isset($_POST['submit_crop']))
{
  include_once(HEADER_MANAGER_PATH . 'include/banner.class.php');
  
  $banner = get_banner($_POST['picture_file']);
  $img = new banner_image($banner['PATH']);
  $crop = hm_get_crop_display(array('width'=>$img->get_width(), 'height'=>$img->get_height()));
  
  $img->banner_resize(
    $banner['PATH'],
    $_POST['x'],
    $_POST['y'], 
    $_POST['x2'],
    $_POST['y2'],
    $crop['display_width'],
    $crop['display_height']
    );
  $img->destroy();
  
  $img = new banner_image($banner['PATH']);
  $img->pwg_resize(
    $banner['THUMB'],
    230, 70, 80,
    false, true, true, false
    );
  $img->destroy();
  
  $_SESSION['page_infos'][] = l10n('Banner added');
  pwg_set_session_var('added_banner', $_POST['picture_file']);
  
  redirect(HEADER_MANAGER_ADMIN);
}

// copy picture from gallery
if (isset($_POST['upload_gallery_image']))
{
  $query = '
SELECT
    file,
    path, 
    coi, 
    width, 
    height
  FROM '.IMAGES_TABLE.'
  WHERE id = '. (int)@$_POST['picture_id'] .'
;';
  $result = pwg_query($query);
  
  if (!pwg_db_num_rows($result))
  {
    array_push($page['errors'], l10n('Unknown picture id'));
  }
  else
  {
    $picture = pwg_db_fetch_assoc($result);
    $picture['filename'] = basename($picture['path']);
    
    copy(PHPWG_ROOT_PATH . $picture['path'], HEADER_MANAGER_DIR . $picture['filename']);
    
    define('IN_CROP', true);
  }
}
// upload new picture
else if (isset($_POST['upload_new_image']))
{
  $file = $_FILES['new_image'];
  
  if ($file['error'] > 0) 
  {
    array_push($page['errors'], l10n('Unknown upload error'));
  }
  else if ( !in_array($file['type'], array('image/jpeg','image/png','image/gif')) )
  {
    array_push($page['errors'], l10n('Incorrect file type,').' '.sprintf(l10n('Allowed file types: %s.'), 'jpg, png, gif'));
  }
  
  if (count($page['errors']) == 0)
  {
    $file['filename'] = date('Ymd').'-'.uniqid().'.'.get_extension($file['name']);
    move_uploaded_file($file['tmp_name'], HEADER_MANAGER_DIR . $file['filename']);
    
    list($width, $height) = getimagesize(HEADER_MANAGER_DIR . $file['filename']);
    $picture = array(
      'file' => $file['name'],
      'filename' => $file['filename'],
      'width' => $width,
      'height' => $height,
      );
      
    define('IN_CROP', true);
  }
}

// croping template
if (defined('IN_CROP'))
{
  // save default size configuration
  $conf['header_manager']['width'] = intval($_POST['width']);
  $conf['header_manager']['height'] = intval($_POST['height']);
  conf_update_param('header_manager', serialize($conf['header_manager']));
    
  $picture['banner_src'] = HEADER_MANAGER_DIR . $picture['filename'];
  
  $template->assign(array(
    'IN_CROP' => true,
    'picture' => $picture,
    'crop' => hm_get_crop_display($picture),
    ));
}
// upload form
else
{
  include_once(PHPWG_ROOT_PATH . 'admin/include/functions_upload.inc.php');
  
  $upload_max_filesize = min(
    get_ini_size('upload_max_filesize'),
    get_ini_size('post_max_size')
    );
    
  $upload_max_filesize_shorthand = 
    ($upload_max_filesize == get_ini_size('upload_max_filesize')) ?
    get_ini_size('upload_max_filesize', false) :
    get_ini_size('post_max_filesize', false);
    
  $template->assign(array(
    'upload_max_filesize' => $upload_max_filesize,
    'upload_max_filesize_shorthand' => $upload_max_filesize_shorthand,
    'BANNER_WIDTH' => $conf['header_manager']['width'],
    'BANNER_HEIGHT' => $conf['header_manager']['height'],
    ));
}

$template->set_filename('header_manager', dirname(__FILE__).'/template/add.tpl');

?>