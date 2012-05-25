<h1 class="page-header"><?php echo lang('sl_custom_header'); ?></h1>
	
<div class="container-fluid">
    <div class="row-fluid">
        <?php if (isset($storyline)) : ?>
		<div class="span8">
			<h4>Title:</h4>
			<?php echo($storyline->title); ?>
			
			<h4>Description:</h4>
			<?php echo($storyline->description); ?>
			
        </div> <!-- /main -->
		
		<div class="span4">
			<?php echo anchor('/storylines/','Back to Storylines List'); ?>
			
			<h3><?php echo lang('sl_meta') ?></h3>
			<table class="table table-bordered table-striped">
			<?php 
			if (isset($storyline->created_on) && !empty($storyline->created_on)) : ?>		
			<tr>
				<td><?php echo lang('sl_created'); ?>:</td>
				<td><?php echo date('m/d/Y h:i:s', $storyline->created_on); ?> by 
				<?php echo anchor('/users/profile/'.$storyline->created_by,find_author_name($storyline->created_by)); ?></td>
			</tr>
			<?php endif;
			if (isset($storyline->modified_on) && !empty($storyline->modified_on)) : ?>		
			<tr>
				<td><?php echo lang('sl_modified'); ?>:</td>
				<td><?php echo date('m/d/Y h:i:s', $storyline->modified_on); ?></td>
			</tr>
			<?php endif;
			if (isset($storyline->flagged) && $storyline->flagged == 1) : ?>		
			<tr>
				<td><div class="label label-important"><?php echo lang('sl_flagged'); ?></div></td>
				<td><?php echo lang('sl_storyline_flagged'); ?></td>
			</tr>
			<?php endif; ?>
			</table>
		</div>
		<?php 
		else: ?>
		<div class="span12">
		Sorry. Storyline Details could not be found. it's possible the storylines was removed or renamed.
		</div>
		<?php
		endif; ?>
	</div>
</div>