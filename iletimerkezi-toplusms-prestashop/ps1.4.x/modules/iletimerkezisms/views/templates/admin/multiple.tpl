<div class="panel">
    <h3><i class="icon-cogs"></i>Toplu SMS</h3>
    <div class="row">
      <form action="{$action_multiple}" method="post">
      <fieldset>
        <div class="form-group">
          <label for="iletimerkezi_customer_group" class="control-label col-lg-3 ">Müşteri Grupları :</label>
          <div class="col-lg-9">
            <select id="iletimerkezi_customer_group" name="iletimerkezi_customer_group">
            {foreach from=$groups item=group}
            <option value="{$group.id_group}">{$group.name}</option>
            {/foreach}
          </select> 
            <p></p> 
          </div>           
        </div>
        <div class="form-group">
          <label for="iletimerkezi_message" class="control-label col-lg-3 ">Mesaj :</label>
          <div class="col-lg-9">
            <textarea id="iletimerkezi_message" name="iletimerkezi_message" cols="60" rows="5" onkeypress="smsCalculator('');" onkeyup="smsCalculator('');"></textarea>
            <input type="hidden" id="bulk" name="bulk" value="1">
            <p>(Mesajin içinde %firstname% %lastname% degiskenini kullanabilirsiniz.)</p> 
            <div class="span7" style="margin-left: 0;">
              <p>Mesaj sayısı: <span id="smsCount" style="font-weight: bold;">1</span><br>
                Karakter sayısı: <span id="characterCount" style="font-weight: bold;">0</span></p>
            </div>
          </div>           
        </div>
        <div class="clearfix">&nbsp;</div>
        <div class="panel-footer">
          <button type="submit" value="1" name="submitModule" class="btn btn-default pull-right">
            <i class="process-icon-save"></i> Gönder
          </button>
        </div>
      </fieldset>
    </form>
    </div>
</div>
<script type="text/javascript">

function smsCalculator(sent_panel) {
        
  var textarea_id = 'iletimerkezi_message';
  
  var sms_count_id = 'smsCount';
  var character_count_id = 'characterCount';
  var pre_sign_label = 'pre_sign_label';

  var text = $('#'+textarea_id).val();
  var total_characters = text.length;


  var sent_type = 2;

  if(sent_type==1||sent_type==2) {
      var max_limit = 1050;
      if(sent_panel!='template_message'&&sent_type==1)
          var sign_count = $('#'+pre_sign_label).val().length + 1;
      else
          var sign_count = 0;

      total_characters += sign_count;

      var sms_count = $('#'+sms_count_id).html();

      if(total_characters < 161) {
          var sms_count = $('#'+sms_count_id).html('1');
      } else if(total_characters < 307) {
          var sms_count = $('#'+sms_count_id).html();

          if(total_characters>160&&sms_count!=2) {
              sms_count = $('#'+sms_count_id).html('2');
              // alertify.alert('1. SMS 160 karakter sınırı aşıldı,mesajınız gönderilecek ancak, bakiyenizden 2 SMS düşülecektir.');
          }
      } else if(total_characters < 460) {

          var sms_count = $('#'+sms_count_id).html();

          if(total_characters>306&&sms_count!=3) {
              sms_count = $('#'+sms_count_id).html('3');
              // alertify.alert('2. SMS 306 karakter sınırı aşıldı,mesajınız gönderilecek ancak, bakiyenizden 3 SMS düşülecektir.');
          }
      } else if(total_characters < 613) {
          var sms_count = $('#'+sms_count_id).html();

          if(total_characters>460&&sms_count!=4) {
              sms_count = $('#'+sms_count_id).html('4');
              // alertify.alert('3. SMS 459 karakter sınırı aşıldı,mesajınız gönderilecek ancak, bakiyenizden 4 SMS düşülecektir.');
          }
      } else if(total_characters < 766) {
          var sms_count = $('#'+sms_count_id).html();

          if(total_characters>613&&sms_count!=5) {
              sms_count = $('#'+sms_count_id).html('5');
              // alertify.alert('4. SMS 612 karakter sınırı aşıldı,mesajınız gönderilecek ancak, bakiyenizden 5 SMS düşülecektir.');
          }
      } else if(total_characters < 919) {
          var sms_count = $('#'+sms_count_id).html();

          if(total_characters>766&&sms_count!=6) {
              sms_count = $('#'+sms_count_id).html('6');
              // alertify.alert('5. SMS 765 karakter sınırı aşıldı,mesajınız gönderilecek ancak, bakiyenizden 6 SMS düşülecektir.');
          }
      } else if(total_characters < 1051) {
          var sms_count = $('#'+sms_count_id).html();

          if(total_characters>919&&sms_count!=7) {
              sms_count = $('#'+sms_count_id).html('7');
              // alertify.alert('6. SMS 918 karakter sınırı aşıldı,mesajınız gönderilecek ancak, bakiyenizden 7 SMS düşülecektir.');
          }
      } else if(total_characters >= 1050) {
          alertify.alert('Maksimum karakter sınırına ulaştınız daha uzun mesaj yazamazsınız');
          var new_text = text.substring(0,1048);
          $('#' + textarea_id).val(new_text);
      }

      $('#'+character_count_id).html(total_characters);
  }

}


</script>