<div class="panel">
    <div class="panel-heading"><i class="icon-cogs"></i>{$heading_title}</div>
    <div class="panel">
      <div class="alert alert-info">
        Mesaj metninin altında kullanabileceğiniz değişkenlerin anlamları aşağıdaki gibidir. <br />
        <br />
        - <strong>%fistname%</strong> - Müşteri Adı <br />
        - <strong>%lastname%</strong> - Müşteri Soyadı <br />
        - <strong>%telephone%</strong> - Müşteri Telefonu <br />
        - <strong>%email%</strong> - Müşteri E-Postası <br />
        - <strong>%password%</strong> - Müşteri Şifresi <br />
        - <strong>%orderid%</strong> - Sipariş Numarası<br />
        - <strong>%orderreference%</strong> - Sipariş Referans Numarası<br />
        - <strong>%productname%</strong> - Ürün Adı<br />
        - <strong>%productmodel%</strong> - Ürün Modeli<br />
        - <strong>%productquantity%</strong> - Ürün Miktarı<br />
        - <strong>%trackingnumber%</strong> - Kargo Takip Numarası<br />
        <br />
        <strong>Not: </strong>Mesaj alanı boş bırakılan bölümler de mesaj gönderilmeyecektir.<br />
        <strong>Not: </strong>Mesaj alanı durumu aktif olmayan bölümler de mesaj gönderilmeyecektir.<br />
        <strong>Not: </strong>Yeni üye olunan müşterilere mesaj gönderilebilmesi için müşteri kayıt ekranında telefon alanı oluşturulmalıdır.<br />
        <br />
        <p align="right">İletimerkezi SMS 1.07</p>
      </div>
    </div>
    <div class="panel">
      <form action="{$action}" method="post">
      <fieldset>
        <div class="form-group">
          <label for="iletimerkezi_username" class="control-label col-lg-3 ">{$text_iletimerkezi_username} :</label>
          <div class="col-lg-9">
            <input type="text" id="iletimerkezi_username" name="iletimerkezi_username" value="{$value_iletimerkezi_username}" class="fixed-width-lg" /><p></p>
          </div>
        </div>
        <div class="form-group">
          <label for="iletimerkezi_password" class="control-label col-lg-3 ">{$text_iletimerkezi_password} :</label>
          <div class="col-lg-9">
            <input type="password" id="iletimerkezi_password" name="iletimerkezi_password" value="{$value_iletimerkezi_password}" class="fixed-width-lg" /><p></p>
          </div>
        </div>
        <div class="form-group">
          <label for="iletimerkezi_sender" class="control-label col-lg-3 ">{$text_iletimerkezi_sender} :</label>
          <div class="col-lg-9">
            <input type="text" id="iletimerkezi_sender" name="iletimerkezi_sender" value="{$value_iletimerkezi_sender}" class="fixed-width-lg" /><p></p>
          </div>
        </div>
        <div class="form-group">
          <label for="iletimerkezi_admin_gsm" class="control-label col-lg-3 ">{$text_iletimerkezi_admin_gsm} :</label>
          <div class="col-lg-9">
            <input type="text" id="iletimerkezi_admin_gsm" name="iletimerkezi_admin_gsm" value="{$value_iletimerkezi_admin_gsm}" class="fixed-width-lg" /><p></p>
          </div>
        </div>

        <div class="form-group">
          <div class="table-responsive clearfix">
          <table class="table  merged">
            <thead>
              <tr class="nodrag nodrop">
                <th class=""><span class="title_box ">Açıklama</span></th>
                <th class=""><span class="title_box ">Mesaj Şablonu</span></th>
                <th class="fixed-width-lg center"><span class="title_box ">İçerik</span></th>
                <th class=""><span class="title_box ">Durum</span></th>
              </tr>
            </thead>
            <tbody>
              <tr class="odd">
                <td class="">{$text_iletimerkezi_new_member_text} :
                  <p class="help-block">
                    (Mesajin içinde %firstname% %lastname% %telephone% degiskenini kullanabilirsiniz.)
                  </p>
                </td>
                <td class="">
                  <textarea id="iletimerkezi_new_member_text" name="iletimerkezi_new_member_text" cols="60" rows="5" onkeypress="smsCalculatorSetting('new_member_text');" onkeyup="smsCalculatorSetting('new_member_text');">{$value_iletimerkezi_new_member_text}</textarea>
                </td>
                <td class="">
                  <div class="span7" style="margin-left: 0;">
                    <p>Mesaj sayısı: <span id="smsCount_new_member_text" style="font-weight: bold;">1</span><br>
                      Karakter sayısı: <span id="characterCount_new_member_text" style="font-weight: bold;">0</span></p>
                  </div>
                </td>
                <td class="text-right">
                  <input id="iletimerkezi_new_member_status" name="iletimerkezi_new_member_status" value="0" type="hidden">
                  <input id="iletimerkezi_new_member_status" name="iletimerkezi_new_member_status" value="1" {$checked_iletimerkezi_new_member_status} type="checkbox">
                </td>
              </tr>

              <tr class="odd">
                <td class="">{$text_iletimerkezi_new_order_text} :
                  <p class="help-block">
                    (Mesajin içinde %orderid% %orderreference% %productname% %productmodel% %productquantity% %firstname% %lastname% %telephone% degiskenini kullanabilirsiniz.)
                  </p>
                </td>
                <td class="">
                  <textarea id="iletimerkezi_new_order_text" name="iletimerkezi_new_order_text" cols="60" rows="5" onkeypress="smsCalculatorSetting('new_order_text');" onkeyup="smsCalculatorSetting('new_order_text');">{$value_iletimerkezi_new_order_text}</textarea>
                </td>
                <td class="">
                  <div class="span7" style="margin-left: 0;">
                    <p>Mesaj sayısı: <span id="smsCount_new_order_text" style="font-weight: bold;">1</span><br>
                      Karakter sayısı: <span id="characterCount_new_order_text" style="font-weight: bold;">0</span></p>
                  </div>
                </td>
                <td class="text-right">
                  <input id="iletimerkezi_new_order_status" name="iletimerkezi_new_order_status" value="0" type="hidden">
                  <input id="iletimerkezi_new_order_status" name="iletimerkezi_new_order_status" value="1" {$checked_iletimerkezi_new_order_status} type="checkbox">
                </td>
              </tr>

              <tr class="odd">
                <td class="">{$text_iletimerkezi_new_member_text_to_member} :
                  <p class="help-block">
                    (Mesajin içinde %firstname% %lastname% %telephone% %email% %password% degiskenini kullanabilirsiniz.)
                  </p>
                </td>
                <td class="">
                  <textarea id="iletimerkezi_new_member_text_to_member" name="iletimerkezi_new_member_text_to_member" cols="60" rows="5" onkeypress="smsCalculatorSetting('new_member_text_to_member');" onkeyup="smsCalculatorSetting('new_member_text_to_member');">{$value_iletimerkezi_new_member_text_to_member}</textarea>
                </td>
                <td class="">
                  <div class="span7" style="margin-left: 0;">
                    <p>Mesaj sayısı: <span id="smsCount_new_member_text_to_member" style="font-weight: bold;">1</span><br>
                      Karakter sayısı: <span id="characterCount_new_member_text_to_member" style="font-weight: bold;">0</span></p>
                  </div>
                </td>
                <td class="text-right">
                  <input id="iletimerkezi_new_member_status_to_member" name="iletimerkezi_new_member_status_to_member" value="0" type="hidden">
                  <input id="iletimerkezi_new_member_status_to_member" name="iletimerkezi_new_member_status_to_member" value="1" {$checked_iletimerkezi_new_member_status_to_member} type="checkbox">
                </td>
              </tr>

              <tr class="odd">
                <td class="">{$text_iletimerkezi_new_order_text_to_member} :
                  <p class="help-block">
                    (Mesajin içinde %orderid% %orderreference% %productname% %productmodel% %productquantity% %firstname% %lastname% %telephone% degiskenini kullanabilirsiniz.)
                  </p>
                </td>
                <td class="">
                  <textarea id="iletimerkezi_new_order_text_to_member" name="iletimerkezi_new_order_text_to_member" cols="60" rows="5" onkeypress="smsCalculatorSetting('new_order_text_to_member');" onkeyup="smsCalculatorSetting('new_order_text_to_member');">{$value_iletimerkezi_new_order_text_to_member}</textarea>
                </td>
                <td class="">
                  <div class="span7" style="margin-left: 0;">
                    <p>Mesaj sayısı: <span id="smsCount_new_order_text_to_member" style="font-weight: bold;">1</span><br>
                      Karakter sayısı: <span id="characterCount_new_order_text_to_member" style="font-weight: bold;">0</span></p>
                  </div>
                </td>
                <td class="text-right">
                  <input id="iletimerkezi_new_order_status_to_member" name="iletimerkezi_new_order_status_to_member" value="0" type="hidden">
                  <input id="iletimerkezi_new_order_status_to_member" name="iletimerkezi_new_order_status_to_member" value="1" {$checked_iletimerkezi_new_order_status_to_member} type="checkbox">
                </td>
              </tr>

              <tr class="odd">
                <td class="">{$text_iletimerkezi_tracking_number} :
                  <p class="help-block">
                    (Mesajin içinde %orderid% %orderreference% %firstname% %lastname% %telephone% %trackingnumber% degiskenini kullanabilirsiniz.)
                  </p>
                </td>
                <td class="">
                  <textarea id="iletimerkezi_tracking_number" name="iletimerkezi_tracking_number" cols="60" rows="5" onkeypress="smsCalculatorSetting('tracking_number');" onkeyup="smsCalculatorSetting('tracking_number');">{$value_iletimerkezi_tracking_number}</textarea>
                </td>
                <td class="">
                  <div class="span7" style="margin-left: 0;">
                    <p>Mesaj sayısı: <span id="smsCount_tracking_number" style="font-weight: bold;">1</span><br>
                      Karakter sayısı: <span id="characterCount_tracking_number" style="font-weight: bold;">0</span></p>
                  </div>
                </td>
                <td class="text-right">
                  <input id="iletimerkezi_tracking_number_status" name="iletimerkezi_tracking_number_status" value="0" type="hidden">
                  <input id="iletimerkezi_tracking_number_status" name="iletimerkezi_tracking_number_status" value="1" {$checked_iletimerkezi_tracking_number_status} type="checkbox">
                </td>
              </tr>

              {$html}

            </tbody>
          </table>
          </div>
        </div>

        <div class="clearfix">&nbsp;</div>
        <!--
        <div class="margin-form">
          <input type="submit" name="submitModule" value="{$button_value}" class="button" />
        </div>-->

        <div class="panel-footer">
          <button type="submit" value="1" name="submitModule" class="btn btn-default pull-right">
            <i class="process-icon-save"></i> {$button_value}
          </button>
        </div>
      </fieldset>
    </form>
    </div>
</div>
<script type="text/javascript">

function smsCalculatorSetting(id) {

  var textarea_id = 'iletimerkezi_'+id;

  var sms_count_id = 'smsCount_' + id;
  var character_count_id = 'characterCount_' + id;

  var text = $('#'+textarea_id).val();
  var total_characters = text.length;

  var max_limit = 1050;
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
</script>