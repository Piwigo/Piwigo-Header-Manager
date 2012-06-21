<?php
if (!defined('HEADER_MANAGER_PATH')) die('Hacking attempt!');

/**
 * add personal banner to page banner
 */
function header_manager_render($page_banner)
{
  global $conf, $user, $template;
  
  if ($conf['header_manager']['image'] == 'random')
  {
    $banners = list_banners();
    if (!count($banners)) return $page_banner;
    $banner = $banners[ mt_rand(0, count($banners)-1) ];
  }
  else
  {
    $banner = get_banner($conf['header_manager']['image']);
    if (!file_exists($banner['PATH'])) return $page_banner;
  }
  
  // for MontBlancXL and BlancMontXL the banner is displayed as background of the header
  if ( in_array($user['theme'], array('blancmontxl','montblancxl')) )
  {
    $template->append('head_elements',
'<style type="text/css">
#theHeader { background: transparent url('.$banner['PATH'].') center bottom no-repeat; }
</style>'
      );

    if ($conf['header_manager']['display'] == 'image_only')
    {
      $page_banner = null;
    }
    else
    {
      $page_banner = str_replace('%header_manager%', null, $page_banner);
    }
  }
  // no support for Kardon (not enough space)
  else if ($user['theme'] != 'kardon')
  {
    $template->append('head_elements',
'<style type="text/css">
#theHeader div.banner { background:transparent url(\''.$banner['PATH'].'\') center center no-repeat;height:'.$banner['SIZE'][1].'px;line-height:'.($banner['SIZE'][1]-12).'px;font-size:2.5em;color:#fff;text-shadow:0 0 5px #000; }
</style>'
      );
    
    $banner_img = '<div class="banner">'.($conf['header_manager']['display']=='with_title' ? $conf['gallery_title'] : '&nbsp;').'</div>';
    
    if ($conf['header_manager']['display'] == 'with_text')
    {
      $page_banner = str_replace('%header_manager%', $banner_img, $page_banner);
    }
    else
    {
      $page_banner = '<a href="'.get_gallery_home_url().'">'.$banner_img.'</a>';
    }
  }

  return $page_banner;
}

/**
 * Header Manager admin link
 */
function header_manager_admin_menu($menu) 
{
  array_push($menu, array(
    'NAME' => 'Header Manager',
    'URL' => HEADER_MANAGER_ADMIN,
  ));
  return $menu;
}

?>