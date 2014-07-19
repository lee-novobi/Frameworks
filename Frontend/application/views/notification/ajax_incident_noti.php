<?php if($bIsAjax) {?>

<table width="100%" cellspacing="0" cellpadding="0" class="list-zebra" id="tblAlert">
	<thead>
	  <tr class="table-title">
		<th colspan="7"><p class="drag">Remind Incident</p></th>
	  </tr>
	  <tr>
		<th class="t-center wp30">Inc</th>
		<th class="t-center wp30">Action</th>
		<th class="t-center wp30">Created date</th>
	  </tr>
	</thead>
  <tbody>
    <?php } ?>
    <?php if (count($arrIncidentNoti) > 0) { ?>
    <?php foreach ($arrIncidentNoti as $k => $oIncidentNoti) { ?>
    <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
      <td style="width:72px; padding: 2px" class="t-left"><?php echo @$oIncidentNoti['content']; ?></td>
      <td style="width:72px; padding: 2px" class="t-left"><a onclick="CloseNoti(<?php echo INCIDENT_NOTI_TYPE; ?>,<?php echo @$oIncidentNoti['id']; ?>)"><?php echo @$oIncidentNoti['action']; ?></a></td>
      <td style="width:72px; padding: 2px" class="t-left"><?php echo @$oIncidentNoti['created_date']; ?></td>
    </tr>
    <?php } ?>
    <?php } ?>
    <?php if($bIsAjax) {?>
  </tbody>
</table>
<?php } ?>
