<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>


<?php echo form_open($this->uri->uri_string(), 'class="form-vertical"'); ?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span8">

			<h3><?php echo lang('sl_edit_article'); ?></h3>

				<!-- GENERAL DETAILS -->
			<fieldset>
				<legend>Message Details</legend>
					<!-- Subject -->
				<div class="control-group <?php echo form_error('subject') ? 'error' : '' ?>">
					 <label class="control-label" for="subject"><?php echo lang('sl_title') ?></label>
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
			
				<legend>Interactive Response</legend>
					<!-- Reply -->
				<div class="control-group <?php echo form_error('reply') ? 'error' : '' ?>">
					 <label class="control-label" for="reply"><?php echo lang('sl_reply') ?></label>
					<div class="controls">
						<input type="text" class="span6" name="reply" id="reply" value="<?php echo isset($article) ? $article->reply : set_value('reply') ?>" />
						<?php if (form_error('reply')) echo '<span class="help-inline">'. form_error('reply') .'</span>'; ?>
					</div>
				</div>
					<!-- Game Message Type -->
				<?php echo form_dropdown('in_game_message',$game_message_types,isset($article) ? $article->in_game_message : set_value('in_game_message'),lang('sl_in_game_message'),' class="span6" id="in_game_message"'); ?>
			
			</fieldset>
			
			<?php if (isset($results) && is_array($results) && count($results)) : ?>
			
			<!-- RESULTS -->
			<fieldset>
				<legend><?php echo lang('sl_results'); ?></legend>
				<?php
				// TODO: CODE THIS SECTION
				// SHOW INJURY/TRANSACTIONS RESULT OPTIONS IN FULL. TRUNCATE THE REST
				?>
			</fieldset>
			<?php
			endif;
			?>
			
				<!-- CONDITIONS -->
			<fieldset>
				<legend><?php echo lang('sl_conditions'); ?></legend>
				<?php if (isset($conditions) && is_array($conditions) && count($conditions)) : ?>
				<div class="control-group">
					<div class="controls">
					<select name="dropdown_conditions" id="dropdown_conditions">
						<?php 
						foreach ($conditions as $category_name => $options) :
							?>
							<optgroup label="<?php echo $category_name; ?>">
								<?php 
								foreach ($options as $opt_id => $opt_label) : ?>
									<option value="<?php echo $opt_id; ?>"><?php echo $opt_label; ?></option>
								<?php
								endforeach;
								?>
							</optgroup>
						<?php
						endforeach;
						?>
					</select>
						<a href="#" class="btn btn-small"><i class="icon-plus"></i> Add Condition</a>
					</div>
				</div>
				<?php
				endif;
				?>
				
				<table class="table table-striped table-bordered" id="data_objects">
				<thead>
				<tr>
					<th class="column-check"><input class="check-all" type="checkbox" /></th>
					<th width="75%"><?php echo lang('sl_condition'); ?></th>
					<th><?php echo lang('sl_value'); ?></th>
					<th><?php echo lang('sl_actions'); ?></th>
				</tr>
				</thead>
				<?php 
				if (isset($article_conditions) && is_array($article_conditions) && count($article_conditions)) :
				 ?>
				<tfoot>
					<tr>
						<td colspan="3">
							<?php echo lang('bf_with_selected') ?>
							<input type="submit" name="submit" class="btn-danger" id="delete-me" value="<?php echo lang('bf_action_delete')." ".lang('sl_data_object') ?>" onclick="return confirm('<?php echo lang('sl_delete_confirm'); ?>')">
						</td>
					</tr>
				</tfoot>
				<tbody id="data_objects_body">
				<?php
					foreach($article_conditions as $condition) : ?>
				<tr>
					<td><input type="checkbox" name="checked[]" value="<?php echo $condition->id ?>" /></td>
					<td><a href="#" rel="tooltip" class="tooltips" title="<?php echo($condition->description); ?>"><?php echo($condition->name); ?></a></td>
					<td><?php echo($condition->value); ?></td>
					<td>
						<a class="btn btn-small" href="#" rel="condition_edit" id="<?php echo $article->id."|".$condition->id ?>">
							<i class="icon-edit"></i><?php echo lang('sl_edit'); ?>
						</a>
						<a class="btn btn-small" href="#" rel="condition_remove" id="<?php echo $article->id."|".$condition->id ?>">
							<i class=" icon-remove"></i> <?php echo lang('sl_delete'); ?>
						</a>
					</td>
				</tr>
				<?php
					endforeach;?>
				</tbody>
				<?php
				else: ?>
				<tbody>
				<tr>
					<td colspan="4"><?php echo lang('sl_no_conditions'); ?></td>
				</tr>
				</tbody>
				<?php
				endif;
				?>
				</table>
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
		
		<div class="span4">
				<!-- MAIN ACTION BUTTONS -->
			<fieldset>
				<div class="well">
					<input type="submit" name="submit" id="submit" class="btn btn-primary btn-large" value="<?php echo lang('bf_action_save') ?>" />
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
				$trigger_str = '';
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
			
				<!-- META -->
			<fieldset>
				<legend><?php echo lang('sl_meta') ?></legend>
				<table class="table table-bordered table-striped">
				<?php 
				if (isset($article->id) && !empty($article->id)) : ?>		
				<tr>
					<td>Article ID:</td>
					<td><?php echo date('m/d/Y h:i:s', $article->id); ?></td>
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
						<?php echo($storyline->status_name);?>
						</span>
					</td>
				</tr>
				<?php endif;
				
				if (isset($article->created_on) && !empty($article->created_on)) : ?>
				<tr>
					<td>Created:</td>
					<td><?php echo date('m/d/Y h:i:s', $article->created_on); ?> by 
					<?php echo anchor(SITE_AREA.'/users/profile/'.$article->created_by,find_author_name($article->created_by)); ?></td>
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