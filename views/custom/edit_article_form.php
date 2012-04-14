<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>


<?php echo form_open($this->uri->uri_string(), 'class="form-vertical"'); ?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span8">
			<div class="admin-box">
				<h3><?php echo lang('sl_edit_article'); ?></h3>
				
				<div id="condition_modal" class="modal" style="display:none;">
					<div class="modal-header">
						<a href="#" class="close" data-dismiss="modal"></a>
						<h3>Conditions Editor</h3>
					</div>
					<div class="modal-body">
						<div id="modal_waitload" class="well center" style="display:none;">
							<img src="<?php echo(TEMPLATE::theme_url('images/ajax-loader.gif'));?>" width="28" height="28" border="0" align="absmiddle" /><br />Operation in progress. Please wait...
						</div>
						<?php if (isset($conditions)) { ?>
						<div id="modal_ajaxStatusBox" style="display:none;"><div id="modal_ajaxStatus" class="alert"></div></div>
						<select id="condition_select"></select>
						<a href class="btn btn-small" id="add_object_condition">Add</a>
						<span class="help-inline"></span>
						<?php } ?>

						<table class="table table-striped table-bordered" id="conditions_table">
						<tbody>
						<tr><td>&nbsp;</td></tr>
						</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal"><?php echo lang('bf_action_cancel'); ?></a>
						<a href="#" id="save_conditions" class="btn btn-primary"><?php echo lang('bf_action_save'); ?></a>
					</div>
				</div>
				
					<!-- GENERAL DETAILS -->
				<fieldset>
					<legend><?php echo lang('sl_general_header'); ?></legend>
						<!-- Title -->
					<div class="control-group <?php echo form_error('title') ? 'error' : '' ?>">
						 <label class="control-label" for="title"><?php echo lang('sl_title') ?></label>
						<div class="controls">
							<input type="text" class="span7" name="title" id="title" value="<?php echo isset($storyline) ? $storyline->title : set_value('title') ?>" />
							<?php if (form_error('title')) echo '<span class="help-inline">'. form_error('title') .'</span>'; ?>
						</div>
					</div>

						<!-- Description -->
					<div class="control-group <?php echo form_error('description') ? 'error' : '' ?>">
						 <label class="control-label"><?php echo lang('sl_description') ?></label>
						<div class="controls">
							<?php echo form_textarea( array( 'name' => 'description', 'id' => 'description', 'class'=>'span7','rows' => '5', 'cols' => '80', 'value' => isset($storyline) ? $storyline->description : set_value('description') ) )?>
							<?php if (form_error('description')) echo '<span class="help-inline">'. form_error('description') .'</span>'; ?>
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<legend><?php echo lang('sl_message_details'); ?></legend>
					
						<!-- Subject -->
					<div class="control-group <?php echo form_error('subject') ? 'error' : '' ?>">
						 <label class="control-label" for="subject"><?php echo lang('sl_subject') ?></label>
						<div class="controls">
							<input type="text" class="span6" name="subject" id="subject" value="<?php echo isset($article) ? $article->subject : set_value('title') ?>" />
							<?php if (form_error('subject')) echo '<span class="help-inline">'. form_error('subject') .'</span>'; ?>
						</div>
					</div>

						<!-- Text -->
					<div class="control-group <?php echo form_error('text') ? 'error' : '' ?>">
						 <label class="control-label"><?php echo lang('sl_text') ?></label>
						<div class="controls">
							<?php echo form_textarea( array( 'name' => 'text', 'id' => 'text', 'class'=>'span6','rows' => '5', 'cols' => '80', 'value' => isset($article) ? $article->text : set_value('text') ) )?>
							<?php if (form_error('text')) echo '<span class="help-inline">'. form_error('text') .'</span>'; ?>
						</div>
					</div>
					
						<!-- Game Message Type -->
					<?php echo form_dropdown('in_game_message',$game_message_types,isset($article) ? $article->in_game_message : set_value('in_game_message'),lang('sl_in_game_message'),' class="span6" id="in_game_message"'); ?>
				
					<legend><?php echo lang('sl_interactive_reponse'); ?></legend>
					
						<!-- Reply -->
					<div class="control-group <?php echo form_error('reply') ? 'error' : '' ?>">
						 <label class="control-label" for="reply"><?php echo lang('sl_reply') ?></label>
						<div class="controls">
							<input type="text" class="span6" name="reply" id="reply" value="<?php echo isset($article) ? $article->reply : set_value('reply') ?>" />
							<?php if (form_error('reply')) echo '<span class="help-inline">'. form_error('reply') .'</span>'; ?>
							<br /><?php echo lang('sl_reply_note'); ?>
						</div>
					</div>
					
				</fieldset>
				
				<?php if (isset($results) && is_array($results) && count($results)) : ?>
				
				<!-- RESULTS -->
				<fieldset>
					<legend><?php echo lang('sl_results'); ?></legend>
					
					<div id="rslt_waitload" class="well center" style="display:none;">
						<img src="<?php echo(TEMPLATE::theme_url('images/ajax-loader.gif'));?>" width="28" height="28" border="0" align="absmiddle" /><br />Operation in progress. Please wait...
					</div>
					<table class="table table-striped table-bordered" id="results_list_table">
					<thead>
					<tr>
						<th class="column-check"><input class="check-all" type="checkbox" /></th>
						<th width="75%"><?php echo lang('sl_result_affects'); ?></th>
						<th width="75%"><?php echo lang('sl_result'); ?></th>
						<th><?php echo lang('sl_value'); ?></th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>&times;</td>
					</tr>
					</tbody>
					</table>
					<a href="#" class="btn btn-small" id="edit_results"><i class="icon-pencil"></i> Edit Results</a>
				</fieldset>
				<?php
				endif;
				?>
				
					<!-- CONDITIONS -->
				<fieldset>
					<legend><?php echo lang('sl_conditions'); ?></legend>
					<div id="cond_waitload" class="well center" style="display:none;">
						<img src="<?php echo(TEMPLATE::theme_url('images/ajax-loader.gif'));?>" width="28" height="28" border="0" align="absmiddle" /><br />Operation in progress. Please wait...
					</div>
					<table class="table table-striped table-bordered" id="conditions_list_table">
					<thead>
					<tr>
						<th width="40%"><?php echo lang('sl_category'); ?></th>
						<th width="40%"><?php echo lang('sl_condition'); ?></th>
						<th><?php echo lang('sl_value'); ?></th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>&times;</td>
					</tr>
					</tbody>
					</table>
					<a href="#" class="btn btn-small" id="edit_conditions"><i class="icon-pencil"></i> Edit Conditions</a>
				
				</fieldset>
				
				<?php if (isset($comment_form) && !empty($comment_form)) : ?>
					<!-- COMMENTS -->
				<fieldset>
					<legend><?php echo lang('sl_comments'); ?></legend>
					<?php echo ($comment_form); ?>
				</fieldset>
				<?php
				endif;
				?>
			</div>
		</div>
		
		<div class="span4">
				<!-- MAIN ACTION BUTTONS -->
			<fieldset>
				<div class="well">
					<input type="submit" name="submit" id="submit" class="btn btn-primary btn-large" value="<?php echo lang('bf_action_save') ?>" />
					<input type="hidden" name="storyline_id" value="<?php echo $storyline->id; ?>" />
				</div>
			</fieldset>
		
				<!-- FREQUENCY/USAGE -->
			<fieldset>
				<legend><?php echo lang('sl_article_frequency') ?></legend>
				
						<!-- Usage -->
				<div class="control-group <?php echo form_error('category_id') ? 'error' : '' ?>">
					 <label class="control-label"><?php echo lang('sl_days_for_usage') ?></label>
					<div class="controls">
						<span class="help-inline"><?php echo lang('sl_days_min') ?>:</span>
						<input type="text" class="span1" name="wait_days_min" id="wait_days_min" value="<?php echo isset($article) ? $article->wait_days_min : set_value('wait_days_min') ?>" />
						<span class="help-inline"><?php echo lang('sl_days') ." &nbsp; ". lang('sl_days_max') ?>:</span>
						<input type="text" class="span1" name="wait_days_max" id="wait_days_max" value="<?php echo isset($article) ? $article->wait_days_max : set_value('wait_days_max') ?>" />
						<span class="help-inline"><?php echo lang('sl_days') ?></span>
					</div>
				</div>
				
			</fieldset>
			
				<!-- DATA OBJECTS -->
			<fieldset>
				<legend><?php echo lang('sl_data_objects') ?></legend>
				<table class="table table-bordered table-striped">
				<?php
				if (isset($characters) && is_array($characters) && count($characters)) :
					foreach($characters as $data_object) : ?>
					<tr>
						<td><a href="#" rel="tooltip" class="tooltips" title="<?php echo($data_object->description); ?>"><?php echo($data_object->name); ?></a></td>
					</tr>
					<?php
					endforeach;
				else:
					echo '<tr><td>'.lang('sl_no_objects').'</td></tr>';
				endif;
				?>
				</table>
				<?php echo anchor(SITE_AREA. '/custom/storylines/edit/'.$article->storyline_id,lang('sl_edit')); ?>
			</fieldset>
			
				<!-- PREDECESSORS -->
			<fieldset>
				<legend><?php echo lang('sl_predecessors') ?></legend>
				<table class="table table-bordered table-striped">
				<thead>
					<th></th>
					<th>Title [Subject]</th>
					<th>Conditions</th>
					<th>Results</th>
				</thead>
				<tbody>
				<?php
				if (isset($all_articles) && is_array($all_articles) && count($all_articles)) :
					foreach($all_articles as $tmp_article) : ?>
					<tr>
						<?php 
						$checked = (isset($article_perdecessor_ids) && is_array($article_perdecessor_ids) && in_array($tmp_article->id, $article_perdecessor_ids) ? ' checked="checked"' : '');
						$icon_class = '';
						switch ($tmp_article->in_game_message)
						{
							case 1: // LEAGUE NEWS
								$icon_class  = 'icon-list-alt';
								break;
							case 2: // PERSONAL MESSAGE
								$icon_class  = 'icon-inbox';
								break;
							case 3: // NO MESSAGE (Replies)
								$icon_class  = 'icon-remove';
								break;
							
						}
						?>
						<td><input type="checkbox" name="pred_ids[]" class="condition" value="<?php echo($tmp_article->id); ?>"<?php echo($checked); ?> /></td>
						<td>
						<i class="<?php echo $icon_class; ?>"></i> 
						<?php 
						$dispSub = limit_text((isset($tmp_article->title) ? $tmp_article->title : $tmp_article->subject),100);
						echo($dispSub); ?></td>
						<td><?php echo($tmp_article->condition_count); ?></td>
						<td><?php echo($tmp_article->result_count); ?></td>
					</tr>
					<?php
					endforeach;
				else:
					echo '<tr><td colspan="4">'.lang('sl_no_predecessors').'</td></tr>';
				endif;
				?>
				</tbody>
				</table>
			</fieldset>
			
				<!-- META -->
			<fieldset>
				<legend><?php echo lang('sl_meta') ?></legend>
				<table class="table table-bordered table-striped">
				<?php 
				if (isset($article->id) && !empty($article->id)) : ?>		
				<tr>
					<td>Article ID:</td>
					<td><?php echo $article->id; ?></td>
				</tr>
				<?php endif;
				
				if (isset($storyline) && !empty($storyline->id) && !empty($storyline->title)) : ?>
				<tr>
					<td>Storyline:</td>
					<td><?php echo anchor(SITE_AREA.'/custom/storylines/edit/'.$storyline->id,$storyline->title); ?></td>
				</tr>
				<?php endif;
				
				if (isset($storyline) && !empty($storyline->publish_status_id)) : ?>
				<tr>
					<td>Storyline Status:</td>
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
						<?php echo($storyline->publish_status_name);?>
						</span>
					</td>
				</tr>
				<?php endif;
				
				if (isset($article->created_on) && !empty($article->created_on)) : ?>
				<tr>
					<td>Created:</td>
					<td><?php echo date('m/d/Y h:i:s', $article->created_on); ?> by 
					<?php echo anchor('/users/profile/'.$article->created_by,find_author_name($article->created_by)); ?></td>
				</tr>
				<?php endif;
				
				if (isset($article->modified_on) && !empty($article->modified_on)) : ?>		
				<tr>
					<td>Modified</td>
					<td><?php echo date('m/d/Y h:i:s', $article->modified_on) .'<div class="help-inline">('. anchor(SITE_AREA.'/custom/storylines/history/2/'.$article->id,'History') .')</div>'; ?></td>
				</tr>
				<?php endif; ?>		
				
				</table>
			</fieldset>
		</div>
	</div>
</div>
<?php echo form_close(); ?>