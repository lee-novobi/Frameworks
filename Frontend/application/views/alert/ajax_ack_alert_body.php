<table class="list-zebra" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th class="w80">Message</th>
			<th>Created By</th>
			<th>Created Date</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($arrACK as $oACK){ ?>
		<tr>
			<td><?php echo htmlentities($oACK['msg'], ENT_QUOTES, "UTF-8") ?></td>
			<td><?php echo ($oACK['ack_by']) ?></td>
			<td class="t-center wp110"><?php echo ($oACK['created_date']) ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
