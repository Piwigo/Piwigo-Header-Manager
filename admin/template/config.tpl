{combine_css path=$HEADER_MANAGER_PATH|cat:'admin/template/style.css'}

{footer_script require="jquery"}
jQuery("input[name='display']").change(function() {
  jQuery(".display-help:not(#"+ jQuery(this).val() +")").slideUp();
  jQuery("#"+ jQuery(this).val()).slideDown();
});
jQuery(".showImage").tipTip({
  delay: 0,
  fadeIn: 200,
  fadeOut: 200,
  maxWidth: '300px',
  defaultPosition: 'top'
});
{/footer_script}

<div class="titrePage">
	<h2>Header Manager</h2>
</div>

<form method="post" action="{$CONFIG_URL}" class="properties">
  <fieldset>
    <legend>{'Display'|translate}</legend>
    
    <label><input type="radio" name="display" value="image_only" {if $BANNER_DISPLAY=='image_only'}checked="checked"{/if}> {'Image only'|translate}</label><br>
    
    <label><input type="radio" name="display" value="with_title" {if $BANNER_DISPLAY=='with_title'}checked="checked"{/if}> {'Gallery title above image'|translate}</label><br>
    <div class="display-help" id="with_title" {if $BANNER_DISPLAY!='with_title'}style="display:none;"{/if}>
      <i>{'You can customize the display by adding CSS rules to'|translate}</i>
      <span style="font-family:monospace;font-size:14px;color:#000;background:#eee;padding:0 2px;">#<span style="color:#09f;font-weight:bold;">theHeader</span> <span style="color:#00f;">div</span>.<span style="color:#f00;">banner</span></span>
    </div>
    
    <label><input type="radio" name="display" value="with_text" {if $BANNER_DISPLAY=='with_text'}checked="checked"{/if}> {'With text'|translate}</label><br>
    <div class="display-help" id="with_text" {if $BANNER_DISPLAY!='with_text'}style="display:none;"{/if}>
      <textarea rows="5" cols="50" class="description" name="conf_page_banner">{$CONF_PAGE_BANNER}</textarea>
      {if isset($EXTDESC_BUTTON)}{$EXTDESC_BUTTON}{/if}<br>
      <i>{'Put <b>%header_manager%</b> where you want to display the image.'|translate}</i>
    </div>
    
    <label><input style="margin-top:20px;" type="checkbox" name="banner_on_picture" value="true" {if $BANNER_ON_PICTURE}checked="checked"{/if}> <b>{'Display banner on photo page'|translate}</b></label>
  </fieldset>
  
  <fieldset id="batchManagerGlobal">
    <legend>{'Banner'|translate}</legend>
    
  {if $banners}
    <div class="banner-radio" style="display:block;">
      <input type="radio" name="image" value="random" id="banner-random" {if $BANNER_IMAGE=='random'}checked="checked"{/if}>
      <label for="banner-random"><b>{'Random'|translate}</b></label>
    </div>
    {foreach from=$banners item=image key=name}
    <div class="banner-radio">
      <span class="actions">
        <input type="radio" name="image" value="{$image.NAME}" id="banner-{$image.NAME}" {if $BANNER_IMAGE==$image.NAME}checked="checked"{/if}><br>
      </span>
      <span class="banner-wrapper">
        <a href="{$CONFIG_URL}&amp;delete_banner={$image.NAME}" title="{'Delete'|translate}" onclick="return confirm('{'Are you sure?'|translate|@escape:javascript}');" class="delete-banner">&times;</a>
        <span class="banner-size">{$image.SIZE[0]} &times; {$image.SIZE[1]} px</span>
        <label for="banner-{$image.NAME}" title="{$name}"><img src="{$image.THUMB}" alt="{$image.NAME}"></label>
      </span>
    </div>
    {/foreach}
  {else}
    {'No banner added yet'|translate}
  {/if}
    
    <br><br>
    <a href="{$ADD_IMAGE_URL}">{'Add a banner'|translate}</a>
  </fieldset>

  <p class="formButtons"><input type="submit" name="save_config" value="{'Submit'|translate}" class="submit"></p>
  
  <fieldset>
    <legend>{'Album specific banners'|translate}</legend>
    <i>{'In order to add a specific banner, go to the admin page of the desired album.'|translate}</i>
    
  {if $categories}
    <ul id="album_banners">
    {foreach from=$categories item=cat}
      <li>
        {$cat.NAME}
        <a class="showImage" title="<img src='{$banners[$cat.IMAGE].THUMB}'>"><img src="{$HEADER_MANAGER_PATH}admin/template/image_{$cat.DEEP}.png"></a>
        <a href="{$cat.U_DELETE}" title="{'Restore default banner'|translate}" onclick="return confirm('{'Are you sure?'|translate|@escape:javascript}');"><img src="{$themeconf.admin_icon_dir}/delete.png"></a>
      </li>
    {/foreach}
    </ul>
    
    <p>
      <img src="{$HEADER_MANAGER_PATH}admin/template/image_0.png"> : {'Non recursive'|translate} &bull; 
      <img src="{$HEADER_MANAGER_PATH}admin/template/image_1.png"> : {'Recursive'|translate}
    </p>
  {/if}
  </fieldset>
</form>