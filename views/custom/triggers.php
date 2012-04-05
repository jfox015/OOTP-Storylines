<div class="admin-box">
	<h3><?php echo lang('sl_triggers') ?></h3>

	<ul class="nav nav-tabs" >
		<li <?php echo $filter=='' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url; ?>">Approved</a></li>
		<li <?php echo $filter=='inactive' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=inactive'; ?>">Inactive</a></li>
	</ul>

	<?php echo form_open(current_url()) ;?>

	<table class="table table-striped">
		<thead>
			<tr>
				<th class="column-check"><input class="check-all" type="checkbox" /></th>
				<th style="width: 3em"><?php echo lang('bf_id'); ?></th>
				<th><?php echo lang('sl_slug'); ?></th>
				<th><?php echo lang('sl_name'); ?></th>
			</tr>
		</thead>
		<?php if (isset($triggers) && is_array($triggers) && count($triggers)) : ?>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo lang('bf_with_selected') ?>
					<input type="submit" name="submit" class="btn" value="<?php echo lang('sl_action_activate') ?>">
					<input type="submit" name="submit" class="btn" value="<?php echo lang('sl_action_deactivate') ?>">
					<input type="submit" name="submit" class="btn-danger" id="delete-me" value="<?php echo lang('bf_action_delete') ?>" onclick="return confirm('<?php echo lang('sl_delete_confirm'); ?>')">
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>
		<tbody>

		<?php if (isset($triggers) && is_array($triggers) && count($triggers)) : ?>
			<?php foreach ($triggers as $trigger) : ?>
			<tr>
				<td>
					<input type="checkbox" name="checked[]" value="<?php echo $trigger->id ?>" />
				</td>
				<td><?php echo $trigger->id ?></td>
				<td>
					<a href="<?php echo site_url(SITE_AREA .'/custom/storylines/triggers/edit/'. $trigger->id); ?>"><?php echo $trigger->slug; ?></a>
				</td>
				<td><?php echo $trigger->name ?></td>
			</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="6"><?php echo lang('sl_no_matches_found') ?></td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
	<?php echo form_close(); ?>

	<?php echo $this->pagination->create_links(); ?>

</div>