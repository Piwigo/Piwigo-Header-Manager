<?php
if (!defined('HEADER_MANAGER_PATH')) die('Hacking attempt!');

/**
 * add personal banner to page banner
 */
function header_manager_render($page_banner)
{
  global $conf, $user, $template, $page;
  
  if ( script_basename() == 'picture' and !$conf['header_manager']['banner_on_picture'] )
  {
    return null;
  }
  
  // search banner for a specific category
  if (isset($page['category']))
  {
    // we use the banner configured for this category
    // if no banner is configured we use the banner of the first parent category with a "deep" banner
    // if nothing found we use the default banner
    $query = '
SELECT *
  FROM '.HEADER_MANAGER_TABLE.'
  WHERE
    category_id IN ('.$page['category']['uppercats'].')
    AND (category_id = '.$page['category']['id'].' OR deep = 1)
;';
    $cat_banners = hash_from_query($query, 'category_id');
    
    if (count($cat_banners))
    {
      usort($cat_banners, 'hm_uppercats_sort');
      
      foreach ($cat_banners as $cat_banner)
      {
        $cat_banner = get_banner($cat_banner['image']);
        if ($cat_banner !== false)
        {
          $banner = $cat_banner;
          break;
        }
      }
    }
  }
  
  // use default banner
  if (!isset($banner))
  {
    if ($conf['header_manager']['image'] == 'random')
    {
      $banners = list_banners();
      if (!count($banners)) return $page_banner;
      $banner = $banners[ mt_rand(0, count($banners)-1) ];
    }
    else
    {
      $banner = get_banner($conf['header_manager']['image']);
      if ($banner === false) return $page_banner;
    }
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
#theHeader div.banner { background:transparent url(\''.$banner['PATH'].'\') center center no-repeat;height:'.$banner['SIZE'][1].'px;line-height:'.($banner['SIZE'][1]-12).'px;font-size:2.5em;color:#fff;text-shadow:0 0 5px #000;text-align:center; }
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

function hm_uppercats_sort($a, $b)
{
  global $page;
  $ids = explode(',', $page['category']['uppercats']);
  return array_search($a['category_id'], $ids) < array_search($b['category_id'], $ids);
}

?>