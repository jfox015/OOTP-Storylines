<div class="admin-box">
	<h3><?php echo lang('sl_custom_header') ?></h3>

	<ul class="nav nav-tabs" >
		<li <?php echo $filter=='' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url; ?>">Approved</a></li>
		<li <?php echo $filter=='category' ? 'class="active"' : ''; ?> class="dropdown">
			<a href="#" class="drodown-toggle" data-toggle="dropdown">
				By Category <?php echo isset($filter_category) ? ": $filter_category" : ''; ?>
				<b class="caret light-caret"></b>
			</a>
			<ul class="dropdown-menu">
			<?php if (isset($categories) && is_array($categories)) { foreach ($categories as $category) : ?>
				<li>
					<a href="<?php echo site_url(SITE_AREA .'/custom/storylines?filter=category&category_id='. $category->id) ?>">
						<?php echo $category->name; ?>
					</a>
				</li>
			<?php endforeach; } ?>
			</ul>
		</li>
		<li <?php echo $filter=='added' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=added'; ?>">Added</a></li>
		<li <?php echo $filter=='review' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=review'; ?>">In Review</a></li>
		<li <?php echo $filter=='rejected' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=rejected'; ?>">Rejected</a></li>
		<li <?php echo $filter=='archived' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=archived'; ?>">Archived</a></li>
		<li <?php echo $filter=='deleted' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=deleted'; ?>">Deleted</a></li>
		<li <?php echo $filter=='author' ? 'class="active"' : ''; ?> class="dropdown">
			<a href="#" class="drodown-toggle" data-toggle="dropdown">
				By Author <?php echo isset($filter_author) ? ": $filter_author" : ''; ?>
				<b class="caret light-caret"></b>
			</a>
			<ul class="dropdown-menu">
			<?php if (isset($users)) { foreach ($users as $user_id => $display_name) : ?>
				<li>
					<a href="<?php echo site_url(SITE_AREA .'/custom/storylines?filter=author&author_id='. $user_id) ?>">
						<?php echo $display_name; ?>
					</a>
				</li>
			<?php endforeach; } ?>
			</ul>
		</li>
		
	</ul>

	<?php echo form_open(current_url()) ;?>

	<table class="table table-striped">
		<thead>
			<tr>
				<th class="column-check"><input class="check-all" type="checkbox" /></th>
				<th style="width: 3em"><?php echo lang('bf_id'); ?></th>
				<th><?php echo lang('bf_title'); ?></th>
				<th><?php echo lang('sl_article_count'); ?></th>
				<th><?php echo lang('sl_creator'); ?></th>
				<th style="width: 11em"><?php echo lang('sl_category'); ?></th>
				<th style="width: 10em"><?php echo lang('us_status'); ?></th>
			</tr>
		</thead>
		<?php if (isset($storylines) && is_array($storylines) && count($storylines)) : ?>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo lang('bf_with_selected') ?>
					<input type="submit" name="submit" class="btn btn-success" value="<?php echo lang('sl_action_approve') ?>">
					<input type="submit" name="submit" class="btn btn-warning" value="<?php echo lang('sl_action_review') ?>">
					<input type="submit" name="submit" class="btn btn-danger" value="<?php echo lang('sl_action_reject') ?>">
					<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('sl_action_archive') ?>">
					<input type="submit" name="submit" class="btn btn-danger" id="delete-me" value="<?php echo lang('bf_action_delete') ?>" onclick="return confirm('<?php echo lang('sl_delete_confirm'); ?>')">
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>
		<tbody>

		<?php if (isset($storylines) && is_array($storylines) && count($storylines)) : ?>
			<?php foreach ($storylines as $storyline) : ?>
			<tr>
				<td>
					<input type="checkbox" name="checked[]" value="<?php echo $storyline->id ?>" />
				</td>
				<td><?php echo $storyline->id ?></td>
				<td>
					<a href="<?php echo site_url(SITE_AREA .'/custom/storylines/edit/'. $storyline->id); ?>"><?php echo $storyline->title; ?></a>
				</td>
				<td><?php echo $storyline->article_count ?></td>
				<td><?php echo find_author_name($storyline->created_by) ?></td>
				<td><?php echo $storyline->category_name ?></td>
				<td><?php
					$class = '';
					switch ($storyline->publish_status_id)
					{
						case 5: // Archived
							$class = '';
							break;
						case 4: // Rejected
							$class = " label-error";
							break;
						case 3: // Approved
							$class = " label-success";
							break;
						case 2: // In Review
							$class = " label-info";
							break;
						case 1: // Added
						default:
							$class = " label-warning";
							break;
					}
					?>
					<span class="label<?php echo($class); ?>">
					<?php echo($storyline->status_name);?>
					</span>
				</td>
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