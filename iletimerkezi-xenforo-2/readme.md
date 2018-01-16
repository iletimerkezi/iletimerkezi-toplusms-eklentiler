# Xenforo 2.0 - Iletimerkezi/Verify Eklentisi Kurulum Adimlari
- Zip dosyasını açtığınızda çıkan upload klasörü içindeki dosyaları, ftp yardımıyla forumunuzun yüklü olduğu ana dizine yükleyin.
- Admin Panelinde / Eklentiler altında bir önceki adımda yüklediğiniz eklentiyi görüceksiniz, yanındaki yükle düğmesine basarak eklentiyi kurun.
- Eklenti kurulduktan sonra Seçenekler kısmından iletimerkezi.com bilgilerinizi girin.
- Kullanıcının telefon numarasını yazdığı custom field alanını oluşturmanız lazım.
- Admin panelinde admin.php?custom-user-fields/ bu adrese gelin, add field kısmına tıklayın.
- Çıkan formu aşağıdaki bilgilerle doldurun
    - Field ID: iletimerkezi_gsm
    - Title: Cep Telefonu
    - Description:
```
<p id="vcode_verify_error_desc" style="display:none;">Cep telefonunuzda hata var lütfen düzeltip tekrar deneyin.</p>
<button id="vcodeSend" class="button--primary button" type="button" onclick="sendVCode();">Onay Kodu Gönder</button>
<span id="vcode_verify_desc" style="display:none;">Cep telefonunuza gelen onay kodunu aşağıya girin.</span>
<input id="vcode" style="display:none;" type="text" class="input" name="vcode">
<script>
window.onload = function() {
  if($('html').attr('data-app') == 'admin') {
    $('#vcodeSend').hide();
  }
}

function sendVCode() {

    $('#vcodeSend').hide();
    $('#vcode_verify_error_desc').hide();

    var gsm_number = $('.field_iletimerkezi_gsm').val();
    var xfToken    = $("input[name='_xfToken']").val();

    if($('html').attr('data-template') == 'account_details') {
      var url = XF.config.url.fullBase + 'account/verify';
    } else {
      var url = XF.config.url.fullBase + 'register/verify';
    }

    $.ajax({
        type: "POST",
        url: url,
        data: 'gsm_number=' + gsm_number + '&_xfToken=' + xfToken,
        success: function(obj) {
            var status = obj.split(':');
            if(status[0] == 'success') {
                $('#vcode_verify_desc').show();
                $('#vcode').show();
            } else {
                $('#vcode_verify_error_desc').html(status[1]);
                $('#vcode_verify_error_desc').show();
                $('#vcodeSend').show();
            }
        }
    });
}
</script>
```
- Başka bir alana değer girmeden kaydedin.