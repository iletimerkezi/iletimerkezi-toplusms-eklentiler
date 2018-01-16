{include file='./_partials/iletimerkezi-header.tpl'}
<div id="im-container">
    <div class="form-wrapper">

        <ul id="im-tabs" class="nav nav-tabs">
            <li class="active">
                <a href="#bulk">Toplu Mesaj Gönder</a>
            </li>
            <li>
                <a href="#report">Son Gönderilen Mesajlar</a>
            </li>
        </ul>

        <div class="tab-content panel">
            <div class="tab-pane active" id="bulk">
                {$bulk_send_form}
            </div>
            <div class="tab-pane" id="report">
                {include file='./_partials/iletimerkezi-report.tpl'}
            </div>
        </div>

    </div>
    <div class="clearfix"></div>
</div>
<script>
$('#im-tabs a').click(function (e) {
    e.preventDefault()
    $(this).tab('show')
})
</script>