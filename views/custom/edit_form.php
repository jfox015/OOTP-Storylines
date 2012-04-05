<?php if (validation_errors()) : ?>
	<div class="alert alert-block alert-error fade in">
		<a class="close" data-dismiss="alert">&times;</a>
		<?php echo validation_errors(); ?>
	</div>
<?php endif; ?>

<?php echo form_open($this->uri->uri_string(), 'class="form-vertical"'); ?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span8">

			<h3><?php echo lang('sl_edit_storyline'); ?></h3>

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
			
			<?php if(isset($conditions_objs) && is_array($conditions_objs) && count($conditions_objs)) :
			
				// BUILD CONDITIONS JS OBJECT ARRAY 
				?>
				<script type="text/javascript">
				function Condition(id, elem, value) {
					this.id = (id !== null) ? id : 0;
					this.elem = (elem !== null) ? elem : '';
					this.value = (value !== null) ? value : '';
				}
				function Condition_Obj(id, slug, name, description, type, min, max, options) {
					this.id = (id !== null) ? id : 0;
					this.slug = (slug !== null) ? slug : '';
					this.name = (name !== null) ? name : '';
					this.description = (description !== null) ? description : '';
					this.type = (type !== null) ? type : '';
					this.value_range_min = (min !== null) ? min : 0;
					this.value_range_max = (max !== null) ? max : 0;
					this.options = (options !== null) ? options : 0;
				}
				var conditions_objs = [], options = [];
				<?php 

					$conditions = array();
					foreach ($conditions_objs as $condition) :
					if (isset($condition->options) && !empty($condition->options)) {
							echo('options = [];');
							echo('options[0] = "Select Option";');
							if (strpos($condition->options,"|") !== false) :

								$ops_array = explode("|",$condition->options);
							else:
								$ops_array = array($condition->options);
							endif;
							foreach($ops_array as $option)  :
								$ops = explode(":",$option);
								echo('options[options.length] = { value: "'.$ops[1].'", name: "'.$ops[0].'"};');
							endforeach;
						}
						echo('conditions_objs['.$condition->id.'] = new Condition_Obj('.$condition->id.',"'.$condition->slug.'","'.$condition->name.'","'.$condition->description.'",'.$condition->type_id.','.$condition->value_range_min.','.$condition->value_range_max.', options);');
						$conditions = $conditions + array($condition->id=>$condition->slug);
					endforeach;
				?>
				</script>
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
					<div class="control-group">
						<div class="controls">
							<?php
								echo form_dropdown('condition_select',$conditions,'',' id="modal_condition_select"');
							?>
							<a href class="btn btn-small" id="add_object_condition">Add</a>
							<span class="help-inline"></span>
						</div>
					</div>
					<?php } ?>

					<table class="table table-striped table-bordered">
					<tbody id="conditions_tbody">
					</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal"><?php echo lang('bf_action_save'); ?></a>
					<a href="#" id="save_conditions" class="btn btn-primary"><?php echo lang('bf_action_save'); ?></a>
				</div>
			</div>
			<?php endif; ?>
			
				<!-- DATA OBJECTS -->
			<fieldset>
				<legend><?php echo lang('sl_data_objects'); ?></legend>
				<div class="help-inline">
					<?php echo form_dropdown('data_object_select',$characters_list,'' ,'',' id="data_object_select"'); ?>
					<span class="help-inline"><a class="btn btn-small" href="#" id="add_data_object">
						<i class="icon-plus"></i> <?php echo lang('sl_add_object'); ?></i>
					</a>
					</span>
				</div>
				<div id="obj_waitload" class="well center" style="display:none;">
					<img src="<?php echo(TEMPLATE::theme_url('images/ajax-loader.gif'));?>" width="28" height="28" border="0" align="absmiddle" /><br />Operation in progress. Please wait...
				</div>
				<div id="obj_ajaxStatusBox" style="display:none;"><div id="obj_ajaxStatus" class="alert"></div></div>
				<table class="table table-striped table-bordered" id="data_objects_tbl">
				<thead>
				<tr>
					<th class="column-check"><input class="check-all" type="checkbox" /></th>
					<th width="45%"><?php echo lang('sl_title'); ?></th>
					<th width="20%"><?php echo lang('sl_conditions'); ?></th>
					<th><?php echo lang('sl_actions'); ?></th>
				</tr>
				</thead>
				<?php 
				if (isset($data_objects) && is_array($data_objects) && count($data_objects)) :
				 ?>
				<tfoot>
					<tr>
						<td colspan="3">
							<?php echo lang('bf_with_selected') ?>
							<input type="submit" name="submit" class="btn-danger" id="delete-me" value="<?php echo lang('bf_action_delete')." ".lang('sl_data_object') ?>" onclick="return confirm('<?php echo lang('sl_delete_confirm'); ?>')">
						</td>
					</tr>
				</tfoot>
				<tbody>
				<tr><td>&nbsp;</td></tr>
				<?php
				/*
					foreach($data_objects as $data_object) : ?>
				<tr>
					<td>
						<input type="checkbox" name="checked[]" value="<?php echo $data_object->id ?>" />
					</td>
					<td><a href="#" rel="tooltip" class="tooltips" title="<?php echo($data_object->description); ?>"><?php echo($data_object->name); ?></a></td>
					<td><?php echo($data_object->condition_count); ?></td>
					<td>
						<a class="btn btn-small" href="#" rel="edit_object_cond" id="<?php echo $storyline->id."|".$data_object->id ?>">
							<i class="icon-edit"></i><?php echo lang('sl_edit'); ?>
						</a>
						<a class="btn btn-small" href="#" rel="remove_data_object" id="<?php echo $storyline->id."|".$data_object->id ?>">
							<i class=" icon-remove"></i> <?php echo lang('sl_delete'); ?>
						</a>
					</td>
				</tr>
				<?php
					endforeach;
				*/
					?>
				</tbody>
				<?php
				/*else: ?>
				<tbody>
				<tr>
					<td colspan="4"><?php echo lang('sl_no_objects'); ?></td>
				</tr>
				</tbody>
				<?php
				*/
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
					echo draw_articles($articles);
				?>
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
				
					<!-- Author Status -->
				<?php if (isset($author_statuses) && is_array($author_statuses) && count($author_statuses)) : ?>
				<div class="control-group <?php echo form_error('author_status_id') ? 'error' : '' ?>">
					 <label class="control-label"><?php echo lang('sl_author_status') ?></label>
					<div class="controls">
						<?php
						echo form_dropdown('author_status_id',$author_statuses,(isset($storyline) ? $storyline->author_status_id : set_value('author_status_id')));
						?>
						<?php if (form_error('author_status_id')) echo '<span class="help-inline">'. form_error('author_status_id') .'</span>'; ?>
					</div>
				</div>
				<?php endif; ?>
				
			</fieldset>
			
				<!-- FREQUENCY -->
			<fieldset>
				<legend><?php echo lang('sl_frequency') ?></legend>
						<!-- Category -->
				<?php if (isset($frequencies) && is_array($frequencies) && count($frequencies)) : ?>
				<div class="control-group <?php echo form_error('random_frequency') ? 'error' : '' ?>">
					 <label class="control-label"><?php echo lang('sl_frequency') ?></label>
					<div class="controls">
						<?php 
						echo form_dropdown('random_frequency',$frequencies, isset($storyline) ? $storyline->random_frequency : set_value('random_frequency 	'));
						?>
						<?php if (form_error('random_frequency')) echo '<span class="help-inline">'. form_error('random_frequency') .'</span>'; ?>
					</div>
				</div>
				<?php endif; ?>
			</fieldset>
			
				<!-- TRIGGERS -->
			<fieldset>
				<legend><?php echo lang('sl_triggers') ?></legend>
					<div id="trg_waitload" class="well center" style="display:none;">
						<img src="<?php echo(TEMPLATE::theme_url('images/ajax-loader.gif'));?>" width="28" height="28" border="0" align="absmiddle" /><br />Operation in progress. Please wait...
					</div>
					<div id="trg_ajaxStatusBox" style="display:none;"><div id="trg_ajaxStatus" class="alert"></div></div>
					<table class="table table-bordered table-striped">
					<tr>
						<td id="triggers_list">
					<?php
					$trigger_str = '';
					if (isset($storyline_triggers) && is_array($storyline_triggers) && count($storyline_triggers)) :
						foreach($storyline_triggers as $trigger_id => $trigger_label) : 
						?>
						<div class="help help-inline"><?php echo $trigger_label; ?> <a class="close" rel="remove_trigger" id="<?php echo $trigger_id; ?>"><i class="icon-remove">&times;</i></a></div>
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
								echo form_dropdown('trigger_select',$triggers,'','class="span9" id="triggers"');
							?>
							<button class="btn" type="button" rel="add_trigger">Add</button>
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