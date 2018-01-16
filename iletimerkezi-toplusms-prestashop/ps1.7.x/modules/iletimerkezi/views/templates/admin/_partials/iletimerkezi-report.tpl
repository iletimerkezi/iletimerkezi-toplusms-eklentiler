<div class="table-responsive-row clearfix">
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Number</th>
                <th>Message</th>
                <th>Error</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$reports key=row_id item=report}
            <tr>
                <td>{$report.date_send}</td>
                <td>{$report.number}</td>
                <td>{$report.message}</td>
                <td>{$report.error}</td>
                <td>
                    {if $report.status eq '2'}
                        <span class="label label-success">Gönderildi</span>
                    {elseif $report.status eq '3'}
                        <span class="label label-danger">Gönderilmedi</span>
                    {else}
                        <span class="label label-info">Rapor Bekleniyor</span>
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>