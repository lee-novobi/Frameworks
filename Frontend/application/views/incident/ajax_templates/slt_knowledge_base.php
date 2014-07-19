<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
	<?php if(!empty($arrKnowledgeBase)) { ?>
		<option value="0" kb_link="" bug_type="">Select one...</option>
	<?php foreach ($arrKnowledgeBase as $oKB) { ?>
    <option value="<?php echo $oKB->id ?>" kb_link="<?php echo $oKB->knowledgebase ?>" bug_type="<?php echo $oKB->bug_type ?>"><?php echo trim($oKB->issue_name) ?></option>
    <?php } ?>
    <option value="-1" kb_link="" bug_type="">Not Supported</option>
    <?php } /* end if */ ?>
</select>
<a class="treat-inc-process" id="kb-document-link" href="javascript:;" target="_self" style="display: <?php echo empty($arrKnowledgeBase)? 'none':'' ?>;">Troubleshooting Process Document</a>