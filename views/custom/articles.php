<div class="admin-box">
	<h3><?php echo lang('sl_articles') ?></h3>

	<ul class="nav nav-tabs" >
		<li <?php echo $filter=='' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url; ?>">All</a></li>
		<li <?php echo $filter=='author' ? 'class="active"' : ''; ?> class="dropdown">
			<a href="#" class="drodown-toggle" data-toggle="dropdown">
				By Author <?php echo isset($filter_author) ? ": $filter_author" : ''; ?>
				<b class="caret light-caret"></b>
			</a>
			<ul class="dropdown-menu">
			<?php if (isset($users)) { foreach ($users as $user_id => $display_name) : ?>
				<li>
					<a href="<?php echo $current_url .'?filter=author&author_id='. $user_id; ?>">
						<?php echo $display_name; ?>
					</a>
				</li>
			<?php endforeach; } ?>
			</ul>
		</li>
		<li <?php echo $filter=='deleted' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=deleted'; ?>">Deleted</a></li>
		
	</ul>

	<?php echo form_open(current_url()) ;?>

	<table class="table table-striped">
		<thead>
			<tr>
				<th class="column-check"><input class="check-all" type="checkbox" /></th>
				<th style="width: 10%"><?php echo lang('bf_id'); ?></th>
				<th style="width: 50%"><?php echo lang('sl_title'); ?></th>
				<th style="width: 20%"><?php echo lang('sl_creator'); ?></th>
				<th style="width: 10%"><?php echo lang('sl_created'); ?></th>
				<th style="width: 10%"><?php echo lang('sl_modified'); ?></th>
			</tr>
		</thead>
		<?php if (isset($storylines) && is_array($storylines) && count($storylines)) : ?>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo lang('bf_with_selected') ?>
					<input type="submit" name="submit" class="btn-danger" id="delete-me" value="<?php echo lang('bf_action_delete') ?>" onclick="return confirm('<?php echo lang('sl_delete_confirm'); ?>')">
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>
		<tbody>

		<?php if (isset($articles) && is_array($articles) && count($articles)) : ?>
			<?php foreach ($articles as $article) : ?>
			<tr>
				<td>
					<input type="checkbox" name="checked[]" value="<?php echo $article->id ?>" />
				</td>
				<td><?php echo $article->id ?></td>
				<td>
					<a href="<?php echo site_url(SITE_AREA .'/custom/storylines/articles/edit/'. $article->id); ?>"><?php echo $article->title; ?></a>
				</td>
				<td><?php echo find_author_name($article->created_by) ?></td>
				<td><?php echo date('m/d/Y',$article->created_on); ?>
				<td><?php echo date('m/d/Y',$article->modified_on); ?>
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