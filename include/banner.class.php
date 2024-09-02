<?php
defined('HEADER_MANAGER_PATH') or die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH . 'admin/include/image.class.php');

/**
 * class derivated from pwg_image, with special function for banner creation
 */
class banner_image extends pwg_image
{
  // Starting with PHP 8, a private method can no longer be called by its child class.
  function get_resize_result($destination_filepath, $width, $height, $time=null)
  {
    return array(
      'source'      => $this->source_filepath,
      'destination' => $destination_filepath,
      'width'       => $width,
      'height'      => $height,
      'size'        => floor(filesize($destination_filepath) / 1024).' KB',
      'time'        => $time ? number_format((get_moment() - $time) * 1000, 2, '.', ' ').' ms' : null,
      'library'     => $this->library,
    );
  }

  function banner_resize($destination_filepath, $selection)
  {
    global $conf;
    $starttime = get_moment();

    // width/height
    $source_width  = $this->image->get_width();
    $source_height = $this->image->get_height();

    $crop = array(
      'width' => $selection['x2']-$selection['x'],
      'height' => $selection['y2']-$selection['y'],
      'x' => $selection['x'],
      'y' => $selection['y'],
      );
    
    // maybe resizing/cropping is useless ?
    if ($conf['header_manager']['width'] == $source_width and $conf['header_manager']['height'] == $source_height)
    {
      // the image doesn't need any resize! We just copy it to the destination
      copy($this->source_filepath, $destination_filepath);
      return $this->get_resize_result($destination_filepath, $source_width, $source_height, $starttime);
    }
    
    $this->image->set_compression_quality(90);
    $this->image->strip();
    
    // crop
    $this->image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
    
    // resize to what is displayed on crop screen
    if ($crop['width'] > $conf['header_manager']['width'])
    {
      $this->image->resize($conf['header_manager']['width'], $crop['height']*$conf['header_manager']['width']/$crop['width']);
    }
    
    // save
    $this->image->write($destination_filepath);

    // everything should be OK if we are here!
    return $this->get_resize_result($destination_filepath, $crop['width'], $crop['height'], $starttime);
  }
}
