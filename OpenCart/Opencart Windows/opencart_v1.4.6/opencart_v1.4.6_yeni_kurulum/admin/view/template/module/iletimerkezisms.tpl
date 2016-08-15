<?php echo $header; ?>

  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <?php 
          echo $breadcrumb['separator']; 
          ?>
          <a href="<?php echo $breadcrumb['href']; ?>">
            <?php echo $breadcrumb['text']; ?>
          </a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="left"></div>
  <div class="right"></div>
    <div class="heading">
      <h1 style="background-image: url('view/image/module.png');"><?php echo $heading_title; ?></h1>
      <div class="buttons">
        <a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a>
        <a href="<?php echo $cancel; ?>" class="button"><span><?php echo $button_cancel; ?></span></a>
      </div>
    </div>
      <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span style="color:red;">Mevcut Bakiyeniz</span></td>
            <td>
              <span style="color:red;">
                <?php
                echo $balance;              
                ?>                
              </span>

            </td>
          </tr>

          <tr>
            <td>iletimerkezi.com telefon numaranız:
              <br>
              <span class="help">Üyeliğiniz yoksa <a href="www.iletimerkezi.com/uye-kayidi">www.iletimerkezi.com/uye-kayidi</a> sayfasindan üye olabilirsiniz.</span>
            </td>
            <td>
              <?php
              if(!empty($iletimerkezisms['iletimerkezi_username'])) {
                $iletimerkezisms_username = $iletimerkezisms['iletimerkezi_username'];
              } else {
                $iletimerkezisms_username = '';
              }
              ?>
              <input value="<?php echo $iletimerkezisms_username; ?>" type="input" name="iletimerkezi_username" />
            </td>
          </tr>

          <tr>
            <td>iletimerkezi.com şifreniz:</td>
            <td>
              <?php
              if(!empty($iletimerkezisms['iletimerkezi_password'])) {
                $iletimerkezisms_password = $iletimerkezisms['iletimerkezi_password'];
              } else {
                $iletimerkezisms_password = '';
              }
              ?>
              <input value="<?php echo $iletimerkezisms_password; ?>" type="password" name="iletimerkezi_password" />
            </td>
          </tr>

          <tr>
            <td>Başlık Bilginiz:
              <br>
              <span class="help">
                Hesabınıza tanımlı olan başlığı yazınız.
              </span>
            </td>
            <td>
              <?php
              if(!empty($iletimerkezisms['iletimerkezi_originator'])) {
                $iletimerkezisms_originator = $iletimerkezisms['iletimerkezi_originator'];
              } else {
                $iletimerkezisms_originator = '';
              }
              ?>
              <input value="<?php echo $iletimerkezisms_originator; ?>" type="text" name="iletimerkezi_originator" />
            </td>
          </tr>

          <!-- <tr>
            <td>Günlük sms limiti:</td>
            <td>
              <?php
              if(!empty($iletimerkezisms['iletimerkezi_sms_limit'])) {
                $iletimerkezisms_sms_limit = $iletimerkezisms['iletimerkezi_sms_limit'];
              } else {
                $iletimerkezisms_sms_limit = '';
              }
              ?>
              <input value="<?php echo $iletimerkezisms_sms_limit; ?>" type="text" name="iletimerkezi_sms_limit" />
            </td>
          </tr> -->

          <tr>
            <td>Yeni üyeye, kullanıcı adı ve şifresi sms olarak gönderilsin:
              <br>
              <span class="help">
                Kullanabileceğiniz değişkenler<br>
                %firstname%<br>
                %lastname%<br>
                %telephone%<br>
                %email%<br>
                %password%
              </span>

            </td>
            <td>
              <?php
              if(!empty($iletimerkezisms['iletimerkezi_sms_password_text'])) {
                $iletimerkezisms_sms_password_text = $iletimerkezisms['iletimerkezi_sms_password_text'];
              } else {
                $iletimerkezisms_sms_password_text = '';
              }
              ?>
              <textarea rows="10" cols="60" name="iletimerkezi_sms_password_text"><?php echo $iletimerkezisms_sms_password_text; ?></textarea>

            </td>
          </tr>

          <tr>
            <td>
              Yeni bir sipariş oluşturulduğunda müşteriye sms gitsin:
              <span class="help">
                Kullanabileceğiniz değişkenler<br>
                %orderid%<br>
                %productname%<br>
                %productmodel%<br>
                %productquantity%<br>
                </span>
            </td>
            <td>                
                <?php
                if(!empty($iletimerkezisms['iletimerkezi_order_customer_notify_text'])) {
                  $iletimerkezi_order_customer_notify_text = $iletimerkezisms['iletimerkezi_order_customer_notify_text'];
                } else {
                  $iletimerkezi_order_customer_notify_text = '';
                }
                ?>
                <textarea rows="10" cols="60" name="iletimerkezi_order_customer_notify_text"><?php echo $iletimerkezi_order_customer_notify_text; ?></textarea>
            </td>
          </tr>
          
          <tr>
            <td>
                Sitenize yeni bir üye geldiğinde bilgileri sms olarak yazacağınız numaraya gönderilsin:
                <span class="help">
                Kullanabileceğiniz değişkenler<br>
                %firstname%<br>
                %lastname%<br>
                %telephone%
                </span>
            </td>
            <td>
                <?php
                if(!empty($iletimerkezisms['iletimerkezi_member_login'])) {
                  $iletimerkezisms_member_login = $iletimerkezisms['iletimerkezi_member_login'];
                } else {
                  $iletimerkezisms_member_login = '';
                }
                ?>
                <input value="<?php echo $iletimerkezisms_member_login; ?>" type="text" name="iletimerkezi_member_login" placeholder="GSM numaranız"/>
                <br>
                <?php
                if(!empty($iletimerkezisms['iletimerkezi_member_login_text'])) {
                  $iletimerkezisms_member_login_text = $iletimerkezisms['iletimerkezi_member_login_text'];
                } else {
                  $iletimerkezisms_member_login_text = '';
                }
                ?>
                <textarea rows="10" cols="60" name="iletimerkezi_member_login_text"><?php echo $iletimerkezisms_member_login_text; ?></textarea>
            </td>
          </tr>

          <tr>
            <td>
              Yeni bir sipariş geldiğinde sms olarak yazacağınız numaraya gönderilsin:
              <span class="help">
                Kullanabileceğiniz değişkenler<br>
                %orderid%<br>
                %productname%<br>
                %productmodel%<br>
                %productquantity%<br>
                </span>
            </td>
            <td>
                <?php
                if(!empty($iletimerkezisms['iletimerkezi_order_notify_gsm'])) {
                  $iletimerkezisms_order_notify_gsm = $iletimerkezisms['iletimerkezi_order_notify_gsm'];
                } else {
                  $iletimerkezisms_order_notify_gsm = '';
                }
                ?>
                <input value="<?php echo $iletimerkezisms_order_notify_gsm; ?>" type="text" name="iletimerkezi_order_notify_gsm" placeholder="Gsm Numarası"/>
                <br>
                <?php
                if(!empty($iletimerkezisms['iletimerkezi_order_notify_text'])) {
                  $iletimerkezisms_order_notify_text = $iletimerkezisms['iletimerkezi_order_notify_text'];
                } else {
                  $iletimerkezisms_order_notify_text = '';
                }
                ?>
                <textarea rows="10" cols="60" name="iletimerkezi_order_notify_text"><?php echo $iletimerkezisms_order_notify_text; ?></textarea>
            </td>
          </tr>

          <tr>
            <td>
                Ürünün kargo durumu değiştiği zaman müşterinize sms gönderilsin:
                <span class="help">
                Kullanabileceğiniz değişkenler<br>
                %orderid%<br>
                %firstname%<br>
                %lastname%<br>
                %status%<br>
                %comment%
                </span>
            </td>
            <td>
              <select id="shipping_status" onchange="getShippingText();">
                <option>Kargo Durumu</option>
                <?php
                foreach ($order_statuses as $key => $value) {
                    echo '<option value="'.$value['order_status_id'].'">'.$value['name'].'</option>';
                }
                ?>
              </select>
              <br>
              <?php
                foreach ($order_statuses as $key => $value) {
                  if(!empty($iletimerkezisms['iletimerkezi_shipping_text_'.$value['order_status_id']])) {
                    $iletimerkezisms_shipping_text = $iletimerkezisms['iletimerkezi_shipping_text_'.$value['order_status_id']];
                  } else {
                    $iletimerkezisms_shipping_text = '';
                  }
              ?>
                  <textarea style="display:none;" class="shipping_text" id="shipping_<?php echo $value['order_status_id']; ?>" rows="10" cols="60" name="iletimerkezi_shipping_text_<?php echo $value['order_status_id']; ?>" ><?php echo $iletimerkezisms_shipping_text; ?></textarea>
              <?php
              }
              ?>

            </td>
          </tr>

        </table>
      </form>
      </div>
  </div>

<script type="text/javascript">
function getShippingText() {
  var shippingText = $('#shipping_status').val();
  $('.shipping_text').hide();
  $('#shipping_'+shippingText).show();

}
</script>
<?php echo $footer; ?>