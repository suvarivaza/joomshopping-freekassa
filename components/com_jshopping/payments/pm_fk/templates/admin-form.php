
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable" width="100%">
            <?php foreach ($form_fields as $key => $data): ?>
				<?php if ($data['type'] !== 'select'): ?>
	                <tr>
	    				<td class="key">
	    					<?= $data['name'] ?>
	    				</td>
	    				<td>
							<?php if (!$data['readonly']): ?>
	    						<input type="<?= $data['type'] ?>" class="inputbox" name="pm_params[<?= $key ?>]" size="<?= $data['size'] ? $data['size'] : '45' ?>" value="<?= $params[$key] ?? $data['value'] ?>">
							<?php else: ?>
								<input type="<?= $data['type'] ?>" class="inputbox" name="pm_params[<?= $key ?>]" size="<?= $data['size'] ? $data['size'] : '45' ?>" value="<?= $data['value'] ?>" readonly>
							<?php endif; ?>
	    					<?= JHTML::tooltip($data['description']) ?>
	    				</td>
	    			</tr>
				<?php else: ?>
					<tr>
	    				<td class="key">
	    					<?= $data['name'] ?>
	    				</td>
	    				<td>
	    					<?= JHTML::_('select.genericlist', $data['value'], 'pm_params[' . $key . ']', 'class = "inputbox" size = "1"', 'status_id', 'name', $params[$key]) ?>
	    					<?= JHTML::tooltip($data['description']) ?>
	    				</td>
	    			</tr>
				<?php endif; ?>
            <?php endforeach; ?>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
