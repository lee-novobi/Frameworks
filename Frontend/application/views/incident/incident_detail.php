<div class="content-header" id="content-header">
    	<h1>Incident Detail</h1> 
    </div>
<table width="100%" cellspacing="0" cellpadding="0" class="table_01 f12">
        <tbody>
          <tr>
			<th width="100px" align="right">Incident ID</th>
			<td colspan="3" style="background-color: #DDD;"><b><?php echo $oIncident['itsm_incident_id']; ?></b></td>
		 </tr>
         <tr>
            <th>Title</th>
            <td colspan="3"><?php echo $oIncident['title'] ?></td>
          </tr>
          <tr>
            <th>Description</th>
            <td colspan="3"><?php echo ( str_replace (array("\r\n", "\n", "\r"), '<br>', $oIncident['description']) ); ?></td>
          </tr>
          <tr>
            <th class="w5">Status</th>
            <td class="w20"><?php echo $oIncident['status'] ?></td>
            <th class="w5">Area</th>
            <td><?php echo $oIncident['area']?></td>
          </tr>
         <tr>
            <th class="w5">Department</th>
            <td id="celDepartment"><?php echo $oIncident['department']?></td>
            <th>Sub Area</th>
            <td id="celSubArea"><?php echo $oIncident['subarea']?></td>
          </tr>
          <tr>
            <th>Outage Start</th>
            <td><?php echo $oIncident['outage_start']?></td>
            <th>Affected Service</th>
            <td id="celProduct" class="w20"><?php echo $oIncident['product']?></td>
          </tr>
          <tr>
            <th>Outage End</th>
            <td><?php echo $oIncident['outage_end']; ?></td>
            <th>Affected CI</th>
            <td id="celAffectedCI">
                <?php echo $oIncident['affected_ci'] ?>
              </select>
            </td>
          </tr>
          <tr>
           <th>Downtime Start</th>
            <td><?php echo $oIncident['downtime_start']; ?></td>
            <th>Assignment Group</th>
            <td id="celAssignmentGroup"><?php echo $oIncident['assignment']; ?></td>
          </tr>
          <tr>
           	<th>Bug category</th>
            <td><?php echo $oIncident['bug_category'] ?></td>
            <th>Assignee</th>
            <td id="celAssignee"><?php echo $oIncident['assignee'] ?></td>
          </tr>
          <tr>
            <th>Bug Unit</th>
            <td id="celBugUnit"><?php echo $oIncident['unit']; ?></td>
            <th>Impact</th>
            <td>
				<?php echo $oIncident['impact_level']; ?>
			</td>
          </tr>
          <tr>
            <th>KB</th>
            <td colspan="3">
				<?php echo (!empty($strIssueName) ? trim($strIssueName) : ''); ?>
			</td>
          </tr>
          <tr>
            <th>Location</th>
            <td>	<?php echo $oIncident['location']; ?></td>
            <th>Urgency</th>
            <td><?php echo $oIncident['urgency_level']; ?></td>
          </tr>
          <tr>
            <th rowspan="4">Related Records</th>
            <td rowspan="4" style="padding-left: 20px"><b>Incident fixed by change: <?php echo ($oIncident['related_id'])?></b><br />
              <br />
              <b>Incident caused by change: <?php echo ($oIncident['related_id_change'])?></b><br />
            </td>
            <th>CCU Times</th>
            <td><?php echo ($oIncident['ccutime'])?></td>
          </tr>
          <tr>
            <th>CCU/Connection/ Transaction</th>
            <td><?php echo $oIncident['user_impacted']?></td>
          </tr>
          <tr>
            <th>E.U.I (by CS channel)</th>
            <td><?php echo $oIncident['customer_case'] ?></td>
          </tr>
          <tr>
            <th>Caused by external service</th>
            <td><input type="checkbox" disabled="disabled" id="chkCauseByExt" name="is_cause_by_ext" <?php if (in_array($oIncident['caused_by_external'], array("t", "true"))) { ?>checked="checked" value="t"<?php }?>>
              &nbsp;&nbsp; <?php echo $oIncident['caused_by_external_dept']; ?>
            </td>
          </tr>
          <tr>
            <th>Critical Asset</th>
            <td colspan="3" id="celCriticalAsset"><?php echo $oIncident['critical_asset'] ?></td>
          </tr>
          <tr>
            <th>Rootcause Category</th>
            <td><?php echo $oIncident['rootcause_category'] ?> </td>
            <th>Is Downtime</th>
            <td><input type="checkbox" disabled="disabled" id="chkIsDowntime" name="is_downtime" <?php if (in_array($oIncident['is_downtime'], array("t", "true"))) { ?>checked="checked" value="t"<?php }?>></td>
          </tr>
          <tr>
            <th>Detector</th>
            <td><?php echo $oIncident['detector'] ?> </td>
            <th>Closure Code</th>
            <td><?php echo $oIncident['closurecode'] ?></td>
          </tr>
        </tbody>
      </table>