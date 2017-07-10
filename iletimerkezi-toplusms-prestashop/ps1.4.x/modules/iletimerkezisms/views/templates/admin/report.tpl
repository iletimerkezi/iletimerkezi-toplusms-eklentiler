<form method="post" action="{$action}" class="form-horizontal clearfix" id="sms">
<input type="hidden" id="submitFiltersms" name="submitFiltersms" value="0">
<div class="panel">
    <h3><i class="icon-cogs"></i> Iletimerkezi Sms</h3>
    <div class="row">
      {if $reports}
      <table class="table">
        <thead>
          <th class="fixed-width-xs center"><span class="title_box">No</span></th>
          <th class="fixed-width-lg text-left"><span class="title_box">Numara</span></th>
          <th><span class="title_box">Mesaj</span></th>
          <th><span class="title_box">Durumu</span></th>
          <th class="fixed-width-lg center"><span class="title_box">Tarih</span></th>
        </thead>
        <tbody>
          {foreach from=$reports item=report}
          <tr>
            <td class="center">{$report.id}</td>
            <td>{$report.number}</td>
            <td>{$report.message}</td>
            <td>{if isset($report.status) && $report.status == 1}Gönderiliyor{elseif $report.status == 2}Gönderildi{else}Gönderilemedi{/if}</td>
            <td>{$report.date_send}</td>
          </tr>
          {/foreach}
        </tbody>
      </table>
      {/if}
    </div>
    <div class="row">
      {if !$simple_header && $list_total > 20 && $reports}
        <div class="col-lg-12">
          <ul class="pagination pull-right">
            <li {if $page <= 1}class="disabled"{/if}>
              <a href="javascript:void(0);" class="pagination-link" data-page="1">
                <i class="icon-double-angle-left"></i>
              </a>
            </li>
            <li {if $page <= 1}class="disabled"{/if}>
              <a href="javascript:void(0);" class="pagination-link" data-page="{$page - 1}">
                <i class="icon-angle-left"></i>
              </a>
            </li>
            {assign p 0}
            {while $p++ < $total_pages}
              {if $p < $page-2}
                <li class="disabled">
                  <a href="javascript:void(0);">&hellip;</a>
                </li>
                {assign p $page-3}
              {else if $p > $page+2}
                <li class="disabled">
                  <a href="javascript:void(0);">&hellip;</a>
                </li>
                {assign p $total_pages}
              {else}
                <li {if $p == $page}class="active"{/if}>
                  <a href="javascript:void(0);" class="pagination-link" data-page="{$p}">{$p}</a>
                </li>
              {/if}
            {/while}
            <li {if $page > $total_pages}class="disabled"{/if}>
              <a href="javascript:void(0);" class="pagination-link" data-page="{$page + 1}">
                <i class="icon-angle-right"></i>
              </a>
            </li>
            <li {if $page > $total_pages}class="disabled"{/if}>
              <a href="javascript:void(0);" class="pagination-link" data-page="{$total_pages}">
                <i class="icon-double-angle-right"></i>
              </a>
            </li>
          </ul>
          <script type="text/javascript">
            $('.pagination-link').on('click',function(e){
              e.preventDefault();
              $('#submitFilter'+'{$table}').val($(this).data("page")).closest("form").submit();
            });
          </script>
        </div>
        {/if}
    </div>
</div>
</form>