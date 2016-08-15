<?php echo $header; ?>

<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> İletimerkezi Kullanıcı Doğrulama</h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table id="parameters" class="list" >
        <thead>
          <tr>
            <td class="left">Mesaj ayarları</td>
            <td class="left"></td>
           
          </tr>
        </thead>
		<tr>
		<td>
		Kullanıcı adı:
		</td>
		<td>
		<input type=text name="phoneverification_userid" value="<?php echo $phoneverification_userid; ?>">
		</td>
		</tr>
		
		<tr>
		<td>
		Şifre:
		</td>
		<td>
		<input type="password" name="phoneverification_apipass" value="<?php echo $phoneverification_apipass; ?>">
		</td>
		</tr>

		<tr>
		<td>
		Başlık bilgisi:
		</td>
		<td>
		<input type="text" name="phoneverification_sender_id" value="<?php echo $phoneverification_sender_id; ?>">
		</td>
		</tr>
		
         <tr>
		<td>
		Müşteriye gidicek mesaj metni
		</td>
		<td>
		<input type=text name="phoneverification_smstemplate" value="<?php echo $phoneverification_smstemplate; ?>">
		</td>
		</tr> 
			    
		
</table>
</td>
</tr>
		
		
        <tfoot>
          
        </tfoot>
      </table>
    </form>

  </div>

</div>
 </div>
<?php echo $footer; ?>