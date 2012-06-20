<?php
if (!defined('HEADER_MANAGER_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH . 'admin/include/image.class.php');

/**
 * class derivated from pwg_image, with special function for banner creation
 */
class banner_image extends pwg_image
{
  function banner_resize($destination_filepath, $x, $y, $x2, $y2, $width, $height)
  {
    global $conf;
    $starttime = get_moment();

    // width/height
    $source_width  = $this->image->get_width();
    $source_height = $this->image->get_height();

    $resize_dimensions = array(
      'width' => $width,
      'height'=> $height,
      'crop' => array(
        'width' => $x2-$x,
        'height' => $y2-$y,
        'x' => $x,
        'y' => $y,
        ),
      );
    
    // maybe resizing/croping is useless ?
    if ( $resize_dimensions['crop']['width'] == $source_width and $resize_dimensions['crop']['height'] == $source_height )
    {
      // the image doesn't need any resize! We just copy it to the destination
      copy($this->source_filepath, $destination_filepath);
      return $this->get_resize_result($destination_filepath, $resize_dimensions['width'], $resize_dimensions['height'], $starttime);
    }
    
    $this->image->set_compression_quality(90);
    $this->image->strip();
    
    // resize to what is displayed on crop screen
    if ($source_width > $conf['header_manager']['width'])
    {
      $this->image->resize($resize_dimensions['width'], $source_height*$resize_dimensions['width']/$source_width);
    }
    
    // crop
    $this->image->crop($resize_dimensions['crop']['width'], $resize_dimensions['crop']['height'], $resize_dimensions['crop']['x'], $resize_dimensions['crop']['y']);
    
    // save
    $this->image->write($destination_filepath);

    // everything should be OK if we are here!
    return $this->get_resize_result($destination_filepath, $resize_dimensions['crop']['width'], $resize_dimensions['crop']['height'], $starttime);
  }
  
  private function get_resize_result($destination_filepath, $width, $height, $time=null)
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
}

?>