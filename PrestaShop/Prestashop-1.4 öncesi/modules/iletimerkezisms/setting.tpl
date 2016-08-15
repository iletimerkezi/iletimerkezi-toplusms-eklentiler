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
        - <strong>%orderid%</strong> - Sipariş numarası<br />
        - <strong>%productname%</strong> - Ürün Adı<br />
        - <strong>%productmodel%</strong> - Ürün Modeli<br />
        - <strong>%productquantity%</strong> - Ürün Miktarı<br />
        <br />
        <strong>Not: </strong>Mesaj alanı boş bırakılan bölümler de mesaj gönderilmeyecektir.<br />
        <br /> 
        <p align="right">İletimerkezi SMS 1.0.2</p>
      </div>
    </div>
    <div class="panel">
      <form action="{$action}" method="post">
      <table border="0" width="800" cellpadding="5" cellspacing="5" id="form">
      <tr>
        <td style="vertical-align: top;" >{$text_iletimerkezi_username} : </td>
        <td><input type="text" id="iletimerkezi_username" name="iletimerkezi_username" value="{$value_iletimerkezi_username}"  /></td>
      </tr>

      <tr>
        <td style="vertical-align: top;" >{$text_iletimerkezi_password} : </td>
        <td><input type="password" id="iletimerkezi_password" name="iletimerkezi_password" value="{$value_iletimerkezi_password}"  /></td>
      </tr>
      
      <tr>
        <td style="vertical-align: top;" >{$text_iletimerkezi_sender} : </td>
        <td><input type="text" id="iletimerkezi_sender" name="iletimerkezi_sender" value="{$value_iletimerkezi_sender}"  /></td>
      </tr>
      
      <tr>
        <td style="vertical-align: top;" >{$text_iletimerkezi_admin_gsm} : </td>
        <td><input type="text" id="iletimerkezi_admin_gsm" name="iletimerkezi_admin_gsm" value="{$value_iletimerkezi_admin_gsm}"  /></td>
      </tr>
      <tr>
        <td style="vertical-align: top;" >{$text_iletimerkezi_new_member_text} : </td>
        <td><textarea id="iletimerkezi_new_member_text" name="iletimerkezi_new_member_text" cols="60" rows="5">{$value_iletimerkezi_new_member_text}</textarea>
            <p class="help-block">
              (Mesajin içinde %firstname% %lastname% %telephone% degiskenini kullanabilirsiniz.)
            </p></td>
      </tr>
      <tr>
        <td style="vertical-align: top;" >{$text_iletimerkezi_new_order_text} : </td>
        <td>
          <textarea id="iletimerkezi_new_order_text" name="iletimerkezi_new_order_text" cols="60" rows="5">{$value_iletimerkezi_new_order_text}</textarea>
            <p class="help-block">
              (Mesajin içinde %orderid% %orderreference% %productname% %productmodel% %productquantity% degiskenini kullanabilirsiniz.)
            </p>
          </td>
      </tr>
      <tr>
        <td style="vertical-align: top;" >{$text_iletimerkezi_new_member_ttm} : </td>
        <td>
          <textarea id="iletimerkezi_new_member_ttm" name="iletimerkezi_new_member_ttm" cols="60" rows="5">{$value_iletimerkezi_new_member_ttm}</textarea>
            <p class="help-block">
              (Mesajin içinde %firstname% %lastname% %telephone% %email% %password% degiskenini kullanabilirsiniz.)
            </p>
          </td>
      </tr>
      <tr>
        <td style="vertical-align: top;" >{$text_iletimerkezi_new_order_ttm} : </td>
        <td>
          <textarea id="iletimerkezi_new_order_ttm" name="iletimerkezi_new_order_ttm" cols="60" rows="5">{$value_iletimerkezi_new_order_ttm}</textarea>
            <p class="help-block">
              (Mesajin içinde %orderid% %orderreference% %productname% %productmodel% %productquantity% degiskenini kullanabilirsiniz.)
            </p>
          </td>
      </tr>
      {$html}
      <tr>
        <td colspan="2" align="center">  
          <!-- <input class="button" name="btnSubmit" value="Update settings" type="submit"> -->

          <button type="submit" value="1" name="submitModule" class="btn btn-default pull-right">
            <i class="process-icon-save"></i> {$button_value}
          </button>
        </td>
      </tr>

    </table>     
    </form>
    </div>
</div>