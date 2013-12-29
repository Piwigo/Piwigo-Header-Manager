{combine_css path=$HEADER_MANAGER_PATH|cat:'admin/template/style.css'}

<div class="titrePage">
	<h2>Header Manager</h2>
</div>

{if $IN_CROP}
{combine_css path="themes/default/js/plugins/jquery.Jcrop.css"}
{combine_script id='jquery.jcrop' load='footer' require='jquery' path='themes/default/js/plugins/jquery.Jcrop.min.js'}

{footer_script require="jquery"}
(function(){
  var jcrop_api;

  jQuery("#jcrop").Jcrop({
    boxWidth: {$crop.box_width}, 
    boxHeight: {$crop.box_height},
    trueSize: [{$picture.width}, {$picture.height}],
    {if $keep_ratio}aspectRatio: {$crop.real_width}/{$crop.real_height},{/if}
    setSelect: [{$crop.l}, {$crop.t}, {$crop.r}, {$crop.b}],
    onChange: function(sel) {
      jQuery("input[name='x']").val(Math.round(sel.x));
      jQuery("input[name='y']").val(Math.round(sel.y));
      jQuery("input[name='x2']").val(Math.round(sel.x2));
      jQuery("input[name='y2']").val(Math.round(sel.y2));
      
      var final_width = Math.min(Math.round(sel.x2-sel.x), {$crop.desired_width}),
          final_height = Math.round((sel.y2-sel.y)*final_width/(sel.x2-sel.x));
      
      jQuery("#width").html(final_width);
      jQuery("#height").html(final_height);
    },
    onRelease: function() {
      jcrop_api.setSelect([{$crop.l}, {$crop.t}, {$crop.r}, {$crop.b}]);
    }
  },
  function() {
    jcrop_api = this;
    {if $keep_ratio}jQuery(".jcrop-holder").addClass('fixed-ratio');{/if}
  });
  
  jQuery('input[name="keep_ratio"]').on('change', function() {
    jcrop_api.setOptions({
      aspectRatio: jQuery(this).prop('checked') ? {$crop.real_width}/{$crop.real_height} : 0
    });
    if (!jQuery(this).prop('checked')) {
      jcrop_api.release();
    }
    jQuery(".jcrop-holder").toggleClass('fixed-ratio');
  });
}());
{/footer_script}

<form method="post" action="{$F_ACTION}">
<fieldset>
  <legend>{'Crop banner image'|translate}</legend>
  {'Choose the part of the image you want to use as your header.'|translate}<br>
  
  <img id="jcrop" src="{$picture.banner_src}">
  
  <ul>
    <li><b>{'Width'|translate}:</b> <span id="width"></span>px</li>
    <li><b>{'Height'|translate}:</b> <span id="height"></span>px</li>
    <li><label><input type="checkbox" name="keep_ratio" {if $keep_ratio}checked{/if}> {'Respect %s aspect ratio'|translate:($crop.desired_width|cat:'/'|cat:$crop.desired_height)}</label></li>
  </ul>
  
  <input type="hidden" name="x">
  <input type="hidden" name="y">
  <input type="hidden" name="x2">
  <input type="hidden" name="y2">
  <input type="hidden" name="picture_file" value="{$picture.filename}">
  
  <input type="submit" name="submit_crop" value="{'Submit'|translate}">
  <input type="submit" name="cancel_crop" value="{'Cancel'|translate}">
</fieldset>
</form>

{else}
{footer_script require="jquery"}
jQuery(".showInfo").tipTip({
  delay: 0,
  fadeIn: 200,
  fadeOut: 200,
  maxWidth: '300px',
});

$("input").bind("keydown", function(event) {
  var keycode = event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode);
  if (keycode == 13 && $("input[name='picture_id']").val() != '') {
    $("input[name='upload_gallery_image']").click();
    return false;
  }
  else {
    return true;
  }
});
{/footer_script}

<form method="post" action="{$F_ACTION}" ENCTYPE="multipart/form-data">
  <fieldset>
    <legend>{'Banner size'|translate}</legend>
    
    <label>
      {'Width'|translate}:
      <input type="text" name="width" size="4" value="{$BANNER_WIDTH}"> px
    </label>
    <br>
    <br>
    <label>
      {'Height'|translate}:
      <input type="text" name="height" size="4" value="{$BANNER_HEIGHT}"> px
    </label>
    <br>
    <br>
    <i>{'For MontBlancXL and BlancMontXL, advised size is 900&times;190.'|translate}</i>
  </fieldset>
  
  <fieldset>
    <legend>{'Select an image'|translate}</legend>
    {'You can upload a custom header image or select one from your gallery. On the next screen you will be able to crop the image.'|translate}
    <br><br>
    
    <b>{'Choose an image from your computer'|translate}</b>
    <blockquote>
      {'Maximum file size: %sB.'|translate:$upload_max_filesize_shorthand} {'Allowed file types: %s.'|translate:'jpg, png, gif'}<br>
      <input type="file" name="new_image">
      <input type="hidden" name="MAX_FILE_SIZE" value="{$upload_max_filesize}">
      <input type="submit" name="upload_new_image" value="{'Upload'|translate}" class="submit">
    </blockquote>
    
    <b>{'or choose a picture from the gallery'|translate}</b>
    <blockquote>
      {'Picture id.'|translate} <a class="icon-info-circled-1 showInfo" title="{'The numeric identifier can be found on the picture edition page, near the thumbnail.'|translate}"></a>
      <input type="text" name="picture_id" size="5">
      <input type="submit" name="upload_gallery_image" value="{'Use'|translate}" class="submit">
    </blockquote>
  </fieldset>
</form>

{/if}