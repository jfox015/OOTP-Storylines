<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('sl_edit_storyline'); ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-vertical"'); ?>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span8">
					<!-- GENERAL DETAILS -->
				<fieldset>
					<legend>General Details</legend>
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
				
				<?php if(isset($characters_list) && is_array($characters_list) && count($characters_list)) : ?>
				<div id="data_object_modal" class="modal" style="display:none;">
					<div class="modal-header">
						<a href="#" class="close" data-dismiss="modal"></a>
						<h3>Characters</h3>
					</div>
					<div class="modal-body">
						<div id="modal_waitload" class="well center" style="display:none;">
							<img src="<?php echo(TEMPLATE::theme_url('images/ajax-loader.gif'));?>" width="28" height="28" border="0" align="absmiddle" /><br />Operation in progress. Please wait...
						</div>
						<div id="modal_ajaxStatusBox" style="display:none;"><div id="modal_ajaxStatus" class="alert"></div></div>

						<div ID="object_form">
							<div class="control-group">
								<label class="control-label"><?php echo lang('sl_select_character') ?></label>
								<div class="controls">
									<?php
									echo form_dropdown('chatacter',$characters_list);
									echo '<span class="help-inline"></span>'; ?>
								</div>
							</div>	
							<div id="object_conditions_box"  style="display:none;">
								<div class="control-group">
									<div class="controls">
										<?php
										echo form_dropdown('object_conditions_select',$character_condition_list); ?>
										<input type="text" class="span1" name="object_condition_val" id="object_condition_val" />
										<a href class="btn btn-small" id="add_object_condition">Add</a>
									</div>
								</div>
							</div>
						</div>
						<div class="controls">
							<?php echo form_textarea( array( 'name' => 'description', 'id' => 'description', 'class'=>'span6','rows' => '5', 'cols' => '80', 'value' => isset($storyline) ? $storyline->description : set_value('description') ) )?>
							<?php if (form_error('description')) echo '<span class="help-inline">'. form_error('description') .'</span>'; ?>
						</div>						
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Cancel</a>
						<a href="#" id="submit_character" class="btn btn-primary">Add Character</a>
					</div>
				</div>
				<?php endif; ?>
				
					<!-- DATA OBJECTS -->
				<fieldset>
					<legend><?php echo lang('sl_data_objects'); ?></legend>
					<div class="help-inline">
						<a class="btn btn-small" href="#" id="add_object">
							<i class="icon-plus"></i> <?php echo lang('sl_add_object'); ?></i>
						</a>
					</div>
					<table class="table table-striped table-bordered" id="data_objects">
					<thead>
					<tr>
						<th class="column-check"><input class="check-all" type="checkbox" /></th>
						<th width="75%"><?php echo lang('sl_title'); ?></th>
						<th><?php echo lang('sl_actions'); ?></th>
					</tr>
					</thead>
					<?php 
					if (isset($characters) && is_array($characters) && count($characters)) :
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
						foreach($characters as $data_object) : ?>
					<tr>
						<td>
							<input type="checkbox" name="checked[]" value="<?php echo $data_object->id ?>" />
						</td>
						<td><a href="#" rel="tooltip" class="tooltips" title="<?php echo($data_object->description); ?>"><?php echo($data_object->name); ?></a></td>
						<td>
							<a class="btn btn-small" href="#" rel="object_edit" id="<?php echo $storyline->id."|".$data_object->id ?>">
								<i class="icon-edit"></i><?php echo lang('sl_edit'); ?>
							</a>
							<a class="btn btn-small" href="#" rel="object_remove" id="<?php echo $storyline->id."|".$data_object->id ?>">
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
						<td colspan="3"><?php echo lang('sl_no_objects'); ?></td>
					</tr>
					</tbody>
					<?php
					endif;
					?>
					</table>
				</fieldset>
					
					<!-- ARTICLES -->
				<fieldset>
					<legend><?php echo lang('sl_articles'); ?></legend>
					<div class="help-inline">
						<a class="btn btn-small" href="#" id="add_article">
							<i class="icon-plus"></i> <?php echo lang('sl_add_article'); ?>
						</a>
					</div>
					<table class="table table-striped table-bordered" id="articles">
					<thead>
					<tr>
						<th class="column-check"><input class="check-all" type="checkbox" /></th>
						<th width="50%"><?php echo lang('sl_title'); ?></th>
						<th><?php echo lang('sl_actions'); ?></th>
					</tr>
					</thead>
					<?php 
					if (isset($articles) && is_array($articles) && count($articles)) : ?>
					<tfoot>
						<tr>
							<td colspan="3">
								<?php echo lang('bf_with_selected') ?>
								<input type="submit" name="submit" class="btn-danger" id="delete-me" value="<?php echo lang('bf_action_delete')." ".lang('sl_article') ?>" onclick="return confirm('<?php echo lang('sl_delete_confirm'); ?>')">
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php	
						foreach($articles as $article) : ?>
					<tr>
						<td>
							<input type="checkbox" name="checked[]" value="<?php echo $article->id ?>" />
						</td>
						<td><?php echo(anchor(SITE_AREA.'/custom/storylines/articles/edit/'.$article->id,$article->subject)); ?></td>
						<td>
							<a class="btn btn-small" href="#" rel="article_details" id="<?php echo $storyline->id."|".$article->id ?>">
								<i class=" icon-zoom-in"></i> <?php echo lang('sl_details'); ?>
							</a>
							<a class="btn btn-small" href="#" rel="article_edit" id="<?php echo $storyline->id."|".$article->id ?>">
								<i class="icon-edit"></i><?php echo lang('sl_edit'); ?>
							</a>
							<a class="btn btn-small" href="#" rel="article_remove" id="<?php echo $storyline->id."|".$article->id ?>">
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
						<td colspan="3"><?php echo lang('sl_no_articles'); ?></td>
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
			
					<!-- CATEGORIES AND STATUS -->
				<fieldset>
					<legend><?php echo lang('sl_status_category') ?></legend>
							<!-- Category -->
					<?php if (isset($categories) && is_array($categories) && count($categories)) : ?>
					<div class="control-group <?php echo form_error('category_id') ? 'error' : '' ?>">
						 <label class="control-label"><?php echo lang('sl_category') ?></label>
						<div class="controls">
							<?php 
							echo form_dropdown('category_id',$categories,set_value('category_id'));
							?>
							<?php if (form_error('category_id')) echo '<span class="help-inline">'. form_error('category_id') .'</span>'; ?>
						</div>
					</div>
					<?php endif; ?>
					
						<!-- Publish Status -->
					<?php if (isset($publish_statuses) && is_array($publish_statuses) && count($publish_statuses)) : ?>
					<div class="control-group <?php echo form_error('publish_status_id') ? 'error' : '' ?>">
						 <label class="control-label"><?php echo lang('sl_publish_status') ?></label>
						<div class="controls">
							<?php
							echo form_dropdown('publish_status_id',$publish_statuses,(isset($storyline) ? $storyline->publish_status_id : set_value('publish_status_id')));
							?>
							<?php if (form_error('publish_status_id')) echo '<span class="help-inline">'. form_error('publish_status_id') .'</span>'; ?>
						</div>
					</div>
					<?php endif; ?>
					
						<!-- Review Status -->
					<?php if (isset($review_statuses) && is_array($review_statuses) && count($review_statuses)) : ?>
					<div class="control-group <?php echo form_error('review_status_id') ? 'error' : '' ?>">
						 <label class="control-label"><?php echo lang('sl_review_status') ?></label>
						<div class="controls">
							<?php
							echo form_dropdown('review_status_id',$review_statuses,(isset($storyline) ? $storyline->review_status_id : set_value('review_status_id')));
							?>
							<?php if (form_error('review_status_id')) echo '<span class="help-inline">'. form_error('review_status_id') .'</span>'; ?>
						</div>
					</div>
					<?php endif; ?>
					
				</fieldset>
				
					<!-- TRIGGERS -->
				<fieldset>
					<legend><?php echo lang('sl_triggers') ?></legend>
						<table class="table table-bordered table-striped">
						<tr>
							<td>
						<?php
						$trigger_str = '';
						if (isset($storyline_triggers) && is_array($storyline_triggers) && count($storyline_triggers)) :
							foreach($storyline_triggers as $trigger_id => $trigger_label) : 
							?>
							<div class="help help-inline"><?php echo $trigger_label; ?> <a class="close" rel="del_trigger" id="<?php echo $trigger_id."|".$storyline->id; ?>">&times;</a></div>
							<?php
							if (!empty($trigger_str)) { $trigger_str .= "|"; }
							$trigger_str .= $trigger_id;
							endforeach;
						else:
							echo lang('sl_no_triggers');
						endif;
						?>
							</td>
						</tr>
						<?php if (isset($trigger_list)) 
						{ 
						?>
						<tr>
							<td>
								<?php
									echo form_dropdown('triggers',$triggers,'','class="span9" id="triggers"');
								?>
								<button class="btn" type="button" rel="trigger_add">Add</button>
								<?php echo form_hidden('storyline_triggers',$trigger_str); ?>
							</td>
						</tr>
						<?php 
						} 
						?>
						</table>
				</fieldset>
				
					<!-- META -->
				<fieldset>
					<legend><?php echo lang('sl_meta') ?></legend>
					<table class="table table-bordered table-striped">
					<?php 
					if (isset($storyline->created_on) && !empty($storyline->created_on)) : ?>		
					<tr>
						<td>Created:</td>
						<td><?php echo date('m/d/Y h:i:s', $storyline->created_on); ?> by 
						<?php echo anchor(SITE_AREA.'/users/profile/'.$storyline->created_by,find_author_name($storyline->created_by)); ?></td>
					</tr>
					<?php endif;
					if (isset($storyline->modified_on) && !empty($storyline->modified_on)) : ?>		
					<tr>
						<td>Modified</td>
						<td><?php echo date('m/d/Y h:i:s', $storyline->modified_on) .'<div class="help-inline">('. anchor('/custom/storylines/history/'.$storyline->id,'History') .')</div>'; ?></td>
					</tr>
					<?php endif; ?>		
					
					</table>
				</fieldset>

			</div>
		</div>
	</div>

<?php echo form_close(); ?>