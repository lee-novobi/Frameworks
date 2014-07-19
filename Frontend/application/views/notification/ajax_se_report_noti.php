<?php if($bIsAjax) {?>

<table width="100%" cellspacing="0" cellpadding="0" class="list-zebra" id="tblAlert">
	<thead>
	  <tr class="table-title">
		<th colspan="7"><p class="drag">Remind SE Report</p></th>
	  </tr>
	  <tr>
		<th class="t-center wp30">Incident</th>
		<th class="t-center wp30">Action</th>
		<th class="t-center wp30">Created date</th>
	  </tr>
	</thead>
  <tbody>
    <?php } ?>
    <?php if (count($arrNotiSEReport) > 0) { ?>
    <?php foreach ($arrNotiSEReport as $k => $oNotiSEReport) { ?>
    <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
      <td style="width:72px; padding: 2px" class="t-left"><?php echo @$oNotiSEReport['content']; ?></td>
      <td style="width:72px; padding: 2px" class="t-left"><a onclick="CloseNoti(<?php echo SE_REPORT_NOTI_TYPE; ?>,<?php echo @$oNotiSEReport['id']; ?>)"><?php echo @$oNotiSEReport['action']; ?></a></td>
      <td style="width:72px; padding: 2px" class="t-left"><?php echo @$oNotiSEReport['created_date']; ?></td>
    </tr>
    <?php } ?>
    <?php } ?>
    <?php if($bIsAjax) {?>
  </tbody>
</table>
<?php } ?>
