<?php
if (!defined('HEADER_MANAGER_PATH')) die('Hacking attempt!');

/**
 * give a list of available banners
 * @param: bool delete_orphans (from unachieved cropping process)
 */
function list_banners($delete_orphans=false)
{ 
  if (!file_exists(HEADER_MANAGER_DIR))
  {
    return array();
  }
  
  $dir = scandir(HEADER_MANAGER_DIR);
  $banners = array();
  
  foreach ($dir as $file)
  {
    if (in_array($file, array('.','..','index.php','.svn'))) continue;
    if (!in_array(strtolower(get_extension($file)), array('jpg','jpeg','png','gif'))) continue;
    if (strpos($file, '-thumbnail')!==false) continue;
    
    $banner = get_banner($file);

    if ($delete_orphans and !file_exists($banner['THUMB']))
    {
      @unlink($banner['PATH']);
    }
    else
    {
      $banners[ get_filename_wo_extension($banner['NAME']) ] = $banner;
    }
  }
  
  return $banners;
}

/**
 * get full size and thumbnail urls and size for a banner
 * @param: string filename
 */
function get_banner($file)
{
  if (file_exists(HEADER_MANAGER_DIR . $file))
  {
    return array(
      'NAME' => $file,
      'PATH' => get_root_url() . HEADER_MANAGER_DIR . $file,
      'THUMB' => get_root_url() . HEADER_MANAGER_DIR . get_filename_wo_extension($file) . '-thumbnail.' . get_extension($file),
      'SIZE' => getimagesize(HEADER_MANAGER_DIR . $file),
      );
  }
  else
  {
    return false;
  }
}

/**
 * get properties of the jCrop window
 * @param: array picture(width, height[, coi])
 * @return: array crop(display_width, display_height, l, r, t, b, coi(x, y))
 */
function hm_get_crop_display($picture)
{
  global $conf;
  
  // find coi
  if (!empty($picture['coi']))
  {
    $picture['coi'] = array(
      'l' => char_to_fraction($picture['coi'][0])*$picture['width'],
      't' => char_to_fraction($picture['coi'][1])*$picture['height'],
      'r' => char_to_fraction($picture['coi'][2])*$picture['width'],
      'b' => char_to_fraction($picture['coi'][3])*$picture['height'],
      );
  }
  else
  {
    $picture['coi'] = array(
      'l' => 0,
      't' => 0,
      'r' => $picture['width'],
      'b' => $picture['height'],
      );
  }
  $crop['coi']['x'] = ($picture['coi']['r']+$picture['coi']['l'])/2;
  $crop['coi']['y'] = ($picture['coi']['b']+$picture['coi']['t'])/2;
  
  $crop['desired_width'] = $conf['header_manager']['width'];
  $crop['desired_height'] = $conf['header_manager']['height'];
  
  if ($picture['width'] > $crop['desired_width'])
  {
    $crop['box_width'] = $crop['desired_width'];
    $crop['box_height'] = round($picture['height']*$crop['box_width']/$picture['width']);
  }
  else
  {
    $crop['box_width'] = $picture['width'];
    $crop['box_height'] = $picture['height'];
  }
  
  $crop['real_width'] = $picture['width'];
  $crop['real_height'] = round($crop['desired_height']*$crop['real_width']/$crop['desired_width']);
  
  $crop['l'] = 0;
  $crop['r'] = $crop['real_width'];
  $crop['t'] = max(0, $crop['coi']['y']-$crop['real_height']/2);
  $crop['b'] = min($picture['height'], $crop['t']+$crop['real_height']);
  
  return $crop;
}
