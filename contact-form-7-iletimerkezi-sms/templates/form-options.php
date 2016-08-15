<div id="sms-sortables" class="meta-box-sortables ui-sortable"><div id="maildiv" class="postbox ">
<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>SMS Bildirimleri</span></h3>
<div class="inside">

<div class="mail-fields">
<div class="half-left">
  
	<div class="mail-field">
  	<label for="wpcf7-sms-recipient">Mesajın iletileceği telefon numarası:</label><br>
  	<input type="text" id="wpcf7-sms-recipient" name="wpcf7-sms[phone]" class="wide" size="70" value="<?php echo $data['phone']; ?>">
	</div>

</div>

<div class="half-right">
	<div class="mail-field">
	<label for="wpcf7-mail-body">Mesaj metni:</label><br>
	<textarea id="wpcf7-mail-body" name="wpcf7-sms[message]" cols="100" rows="2"><?php echo $data['message']; ?></textarea>
	</div>
</div>

<br class="clear">
</div>
</div>
</div>
</div>