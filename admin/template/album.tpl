{combine_css path=$HEADER_MANAGER_PATH|cat:'admin/template/style.css'}

<div class="titrePage">
  <h2><span style="letter-spacing:0">{$CATEGORIES_NAV}</span> &#8250; {'Edit album'|translate} [{'Banner'|translate}]</h2>
</div>

<form method="post" action="{$F_ACTION}" class="properties" id="batchManagerGlobal">
  {if $banners}
    <div class="banner-radio" style="display:block;">
      <input type="radio" name="image" value="default" id="banner-default"{if $BANNER_IMAGE=='default'}checked="checked"{/if}>
      <label for="banner-default"><b>{'Default banner'|translate}</b></label>
    </div>
    {foreach from=$banners item=image}
    <div class="banner-radio">
      <span class="actions">
        <input type="radio" name="image" value="{$image.NAME}" id="banner-{$image.NAME}" {if $BANNER_IMAGE==$image.NAME}checked="checked"{/if}><br>&nbsp;
      </span>
      <span class="banner-wrapper">
        <span class="banner-size">{$image.SIZE[0]} &times; {$image.SIZE[1]} px</span>
        <label for="banner-{$image.NAME}"><img src="{$image.THUMB}" alt="{$image.NAME}"></label>
      </span>
    </div>
    {/foreach}
  {else}
    <p style="text-align:left;">{'No banner added yet'|translate}</p>
  {/if}
  
  <p style="text-align:left;">
    <a href="{$ADD_IMAGE_URL}&redirect={$F_ACTION|urlencode}">{'Add a banner'|translate}</a>
  </p>
    
  {if $banners}
  <p class="actionButtons">
    <label><input type="checkbox" name="deep" value="1" {if $BANNER_DEEP}checked="checked"{/if}> {'Apply to sub-albums'|translate}</label>
    <br><br>
    <input type="submit" name="save_banner" value="{'Submit'|translate}" class="submit">
  </p>
  {/if}
</form>