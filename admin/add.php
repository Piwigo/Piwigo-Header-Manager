<?php
defined('HEADER_MANAGER_PATH') or die('Hacking attempt!');

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
  
  $conf['header_manager']['keep_ratio'] = isset($_POST['keep_ratio']);
  conf_update_param('header_manager', $conf['header_manager']);
  
  $banner = get_banner($_POST['picture_file']);
  
  $img = new banner_image($banner['PATH']);
  $img->banner_resize(
    $banner['PATH'],
    $_POST
    );
  $img->destroy();
  
  $img = new pwg_image($banner['PATH']);
  $img->pwg_resize(
    $banner['THUMB'],
    230, 70, 80,
    false, true, true, false
    );
  $img->destroy();
  
  $_SESSION['page_infos'][] = l10n('Banner added');
  
  if (!empty($_GET['redirect']))
  {
    redirect($_GET['redirect']);
  }
  else
  {
    redirect(HEADER_MANAGER_ADMIN);
  }
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
    $page['errors'][] = l10n('Unknown picture id');
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
    $page['errors'][] = l10n('Unknown upload error');
  }
  else if (!in_array($file['type'], array('image/jpeg','image/png','image/gif')))
  {
    $page['errors'][] = l10n('Incorrect file type,').' '.l10n('Allowed file types: %s.', 'jpg, png, gif');
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
  conf_update_param('header_manager', $conf['header_manager']);

  $picture['banner_src'] = HEADER_MANAGER_DIR . $picture['filename'];
  
  $template->assign(array(
    'IN_CROP' => true,
    'picture' => $picture,
    'crop' => hm_get_crop_display($picture),
    'keep_ratio' => $conf['header_manager']['keep_ratio'],
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

$template->assign('F_ACTION', HEADER_MANAGER_ADMIN . '-add' .
  (!empty($_GET['redirect']) ? '&amp;redirect='.urlencode($_GET['redirect']) : ''));

$template->set_filename('header_manager', realpath(HEADER_MANAGER_PATH . 'admin/template/add.tpl'));
