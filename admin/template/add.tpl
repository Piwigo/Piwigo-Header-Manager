{combine_css path=$HEADER_MANAGER_PATH|@cat:"admin/template/style.css"}

<div class="titrePage">
	<h2>Header Manager</h2>
</div>

{if $IN_CROP}
{combine_css path="themes/default/js/plugins/jquery.Jcrop.css"}
{combine_script id='jquery.jcrop' load='footer' require='jquery' path='themes/default/js/plugins/jquery.Jcrop.min.js'}

{footer_script require="jquery"}
var jcrop_api;

jQuery("#jcrop").Jcrop({ldelim}
    boxWidth: {$crop.display_width}, 
    boxHeight: {$crop.display_height},
    setSelect: [{$crop.l}, {$crop.t}, {$crop.r}, {$crop.b}],
    onChange: jOnChange,
    onRelease: jOnRelease
	},
  function(){ldelim}
    jcrop_api = this;
  });
  
function jOnChange(sel) {ldelim}
	jQuery("input[name='x']").val(sel.x);
	jQuery("input[name='y']").val(sel.y);
	jQuery("input[name='x2']").val(sel.x2);
	jQuery("input[name='y2']").val(sel.y2);
  
  jQuery("#width").html(sel.x2-sel.x);
  jQuery("#height").html(sel.y2-sel.y);
}
  
function jOnRelease() {ldelim}
	jcrop_api.setSelect([{$crop.l}, {$crop.t}, {$crop.r}, {$crop.b}]);
}
{/footer_script}

<form method="post" action="">
<fieldset>
  <legend>{'Crop banner image'|@translate}</legend>
  {'Choose the part of the image you want to use as your header.'|@translate}<br>
  
  <img id="jcrop" src="{$picture.banner_src}" width="{$crop.display_width}" height="{$crop.display_height}">
  
  <ul>
    <li><b>{'Width'|@translate}:</b> <span id="width"></span>px</li>
    <li><b>{'Height'|@translate}:</b> <span id="height"></span>px</li>
  </ul>
  
  <input type="hidden" name="x">
  <input type="hidden" name="y">
  <input type="hidden" name="x2">
  <input type="hidden" name="y2">
  <input type="hidden" name="picture_file" value="{$picture.filename}">
  
  <input type="submit" name="submit_crop" value="{'Submit'|@translate}">
  <input type="submit" name="cancel_crop" value="{'Cancel'|@translate}">
</fieldset>
</form>

{else}
{footer_script require="jquery"}{literal}
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
  } else {
    return true;
  }
});
{/literal}{/footer_script}

<form method="post" action="" ENCTYPE="multipart/form-data">
  <fieldset>
    <legend>{'Default banner size'|@translate}</legend>
    
    <label>
      {'Width'|@translate}:
      <input type="text" name="width" size="4" value="{$BANNER_WIDTH}">px
    </label>
    <br>
    <br>
    <label>
      {'Height'|@translate}:
      <input type="text" name="height" size="4" value="{$BANNER_HEIGHT}">px
    </label>
    <br>
    <br>
    <i>{'For MontBlancXL and BlancMontXL, advised size is 900&times;190.'|@translate}</i>
  </fieldset>
  
  <fieldset>
    <legend>{'Select an image'|@translate}</legend>
    {'You can upload a custom header image or select one from your gallery. On the next screen you will be able to crop the image.'|@translate}
    <br><br>
    
    <b>{'Choose an image from your computer'|@translate}</b>
    <blockquote>
      {'Maximum file size: %sB.'|@translate|@sprintf:$upload_max_filesize_shorthand} {'Allowed file types: %s.'|@translate|@sprintf:'jpg, png, gif'}<br>
      <input type="file" name="new_image">
      <input type="hidden" name="MAX_FILE_SIZE" value="{$upload_max_filesize}">
      <input type="submit" name="upload_new_image" value="{'Upload'|@translate}" class="submit">
    </blockquote>
    
    <b>{'or choose a picture from the gallery'|@translate}</b>
    <blockquote>
      {'Picture id.'|@translate} <a class="showInfo" title="{'The numeric identifier can be found on the picture edition page, near the thumbnail.'|@translate}">i</a>
      <input type="text" name="picture_id" size="5">
      <input type="submit" name="upload_gallery_image" value="{'Use'|@translate}" class="submit">
    </blockquote>
  </fieldset>
</form>

{/if}