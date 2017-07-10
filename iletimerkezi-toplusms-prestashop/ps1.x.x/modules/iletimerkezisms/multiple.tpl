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
            <textarea id="iletimerkezi_message" name="iletimerkezi_message" cols="60" rows="5"></textarea>
            <input type="hidden" id="bulk" name="bulk" value="1">
            <p>(Mesajin içinde %firstname% %lastname% degiskenini kullanabilirsiniz.)</p> 
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