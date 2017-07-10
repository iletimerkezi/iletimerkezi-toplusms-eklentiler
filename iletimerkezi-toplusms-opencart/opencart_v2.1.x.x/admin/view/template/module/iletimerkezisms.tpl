<?php  echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button onclick="$('#form').submit();" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
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
              if(!empty($iletimerkezisms['iletimerkezisms_username'])) {
                $iletimerkezisms_username = $iletimerkezisms['iletimerkezisms_username'];
              } else {
                $iletimerkezisms_username = '';
              }
              ?>
              <input value="<?php echo $iletimerkezisms_username; ?>" type="input" name="iletimerkezisms_username" />
            </td>
          </tr>

          <tr>
            <td>iletimerkezi.com şifreniz:</td>
            <td>
              <?php
              if(!empty($iletimerkezisms['iletimerkezisms_password'])) {
                $iletimerkezisms_password = $iletimerkezisms['iletimerkezisms_password'];
              } else {
                $iletimerkezisms_password = '';
              }
              ?>
              <input value="<?php echo $iletimerkezisms_password; ?>" type="password" name="iletimerkezisms_password" />
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
              if(!empty($iletimerkezisms['iletimerkezisms_originator'])) {
                $iletimerkezisms_originator = $iletimerkezisms['iletimerkezisms_originator'];
              } else {
                $iletimerkezisms_originator = '';
              }
              ?>
              <input value="<?php echo $iletimerkezisms_originator; ?>" type="text" name="iletimerkezisms_originator" />
            </td>
          </tr>

          <!-- <tr>
            <td>Günlük sms limiti:</td>
            <td>
              <?php
              if(!empty($iletimerkezisms['iletimerkezisms_sms_limit'])) {
                $iletimerkezisms_sms_limit = $iletimerkezisms['iletimerkezisms_sms_limit'];
              } else {
                $iletimerkezisms_sms_limit = '';
              }
              ?>
              <input value="<?php echo $iletimerkezisms_sms_limit; ?>" type="text" name="iletimerkezisms_sms_limit" />
            </td>
          </tr> -->

          <tr>
            <td>Yeni üyeye, kullanıcı adı ve şifresi sms olarak gönderilsin:
              <br>
              <span class="help">
                <br>Kullanabileceğiniz değişkenler<br>
                %firstname%<br>
                %lastname%<br>
                %telephone%<br>
                %email%<br>
                %password%
              </span>

            </td>
            <td>
              <?php
              if(!empty($iletimerkezisms['iletimerkezisms_sms_password_text'])) {
                $iletimerkezisms_sms_password_text = $iletimerkezisms['iletimerkezisms_sms_password_text'];
              } else {
                $iletimerkezisms_sms_password_text = '';
              }
              ?>
              <textarea rows="10" cols="60" name="iletimerkezisms_sms_password_text"><?php echo $iletimerkezisms_sms_password_text; ?></textarea>

            </td>
          </tr>

          <tr>
            <td>
                Sitenize yeni bir üye geldiğinde bilgileri sms olarak yazacağınız numaraya gönderilsin:
                <span class="help">
                <br>Kullanabileceğiniz değişkenler<br>
                %firstname%<br>
                %lastname%<br>
                %telephone%
                </span>
            </td>
            <td>
                <?php
                if(!empty($iletimerkezisms['iletimerkezisms_member_login'])) {
                  $iletimerkezisms_member_login = $iletimerkezisms['iletimerkezisms_member_login'];
                } else {
                  $iletimerkezisms_member_login = '';
                }
                ?>
                <input value="<?php echo $iletimerkezisms_member_login; ?>" type="text" name="iletimerkezisms_member_login" placeholder="GSM numaranız"/>
                <br>
                <?php
                if(!empty($iletimerkezisms['iletimerkezisms_member_login_text'])) {
                  $iletimerkezisms_member_login_text = $iletimerkezisms['iletimerkezisms_member_login_text'];
                } else {
                  $iletimerkezisms_member_login_text = '';
                }
                ?>
                <textarea rows="10" cols="60" name="iletimerkezisms_member_login_text"><?php echo $iletimerkezisms_member_login_text; ?></textarea>
            </td>
          </tr>

          <tr>
            <td>
              Yeni bir sipariş oluşturulduğunda müşteriye sms gitsin:
              <span class="help">
                <br>Kullanabileceğiniz değişkenler<br>
                %orderid%<br>
                %productname%<br>
                %productmodel%<br>
                %productquantity%<br>
                </span>
            </td>
            <td>                
                <?php
                if(!empty($iletimerkezisms['iletimerkezisms_order_customer_notify_text'])) {
                  $iletimerkezisms_order_customer_notify_text = $iletimerkezisms['iletimerkezisms_order_customer_notify_text'];
                } else {
                  $iletimerkezisms_order_customer_notify_text = '';
                }
                ?>
                <textarea rows="10" cols="60" name="iletimerkezisms_order_customer_notify_text"><?php echo $iletimerkezisms_order_customer_notify_text; ?></textarea>
            </td>
          </tr>


          <tr>
            <td>
              Yeni bir sipariş geldiğinde sms olarak yazacağınız numaraya gönderilsin:
              <span class="help">
                <br>Kullanabileceğiniz değişkenler<br>
                %orderid%<br>
                %productname%<br>
                %productmodel%<br>
                %productquantity%<br>
                </span>
            </td>
            <td>
                <?php
                if(!empty($iletimerkezisms['iletimerkezisms_order_notify_gsm'])) {
                  $iletimerkezisms_order_notify_gsm = $iletimerkezisms['iletimerkezisms_order_notify_gsm'];
                } else {
                  $iletimerkezisms_order_notify_gsm = '';
                }
                ?>
                <input value="<?php echo $iletimerkezisms_order_notify_gsm; ?>" type="text" name="iletimerkezisms_order_notify_gsm" placeholder="Gsm Numarası"/>
                <br>
                <?php
                if(!empty($iletimerkezisms['iletimerkezisms_order_notify_text'])) {
                  $iletimerkezisms_order_notify_text = $iletimerkezisms['iletimerkezisms_order_notify_text'];
                } else {
                  $iletimerkezisms_order_notify_text = '';
                }
                ?>
                <textarea rows="10" cols="60" name="iletimerkezisms_order_notify_text"><?php echo $iletimerkezisms_order_notify_text; ?></textarea>
            </td>
          </tr>

          <tr>
            <td>
              Sipariş iptalinde yöneticiye sms gitsin:
              <span class="help">
                <br>Kullanabileceğiniz değişkenler<br>
                %firstname%<br>
                %lastname%<br>
                %telephone%<br>
                %email%<br>
                %product%<br>
                %reason%<br>
                %comment%<br>
                </span>
            </td>
            <td> 
                 <?php
                if(!empty($iletimerkezisms['iletimerkezisms_sms_return_gsm'])) {
                  $iletimerkezisms_sms_return_gsm = $iletimerkezisms['iletimerkezisms_sms_return_gsm'];
                } else {
                  $iletimerkezisms_sms_return_gsm = '';
                }
                ?>
                <input value="<?php echo $iletimerkezisms_sms_return_gsm; ?>" type="text" name="iletimerkezisms_sms_return_gsm" placeholder="Gsm Numarası"/>
                <br>               
                <?php
                if(!empty($iletimerkezisms['iletimerkezisms_sms_return_text'])) {
                  $iletimerkezisms_sms_return_text = $iletimerkezisms['iletimerkezisms_sms_return_text'];
                } else {
                  $iletimerkezisms_sms_return_text = '';
                }
                ?>
                <textarea rows="10" cols="60" name="iletimerkezisms_sms_return_text"><?php echo $iletimerkezisms_sms_return_text; ?></textarea>
            </td>
          </tr>

          <tr>
            <td>
                Ürünün kargo durumu değiştiği zaman müşterinize sms gönderilsin:
                <span class="help">
                <br>Kullanabileceğiniz değişkenler<br>
                %orderid%<br>
                %firstname%<br>
                %lastname%<br>
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
                  if(!empty($iletimerkezisms['iletimerkezisms_shipping_text_'.$value['order_status_id']])) {
                    $iletimerkezisms_shipping_text = $iletimerkezisms['iletimerkezisms_shipping_text_'.$value['order_status_id']];
                  } else {
                    $iletimerkezisms_shipping_text = '';
                  }
              ?>
                  <textarea style="display:none;" class="shipping_text" id="shipping_<?php echo $value['order_status_id']; ?>" rows="10" cols="60" name="iletimerkezisms_shipping_text_<?php echo $value['order_status_id']; ?>" ><?php echo $iletimerkezisms_shipping_text; ?></textarea>
              <?php
              }
              ?>

            </td>
          </tr>

        </table>
      </form>
    </div>
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