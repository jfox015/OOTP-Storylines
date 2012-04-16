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
				<h3><?php echo lang('sl_edit_storyline'); ?></h3>
			
					<!-- GENERAL DETAILS -->
				<fieldset>
					<legend style="margin-bottom:0px;"><?php echo lang('sl_general_header'); ?></legend>
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
				<div style="display:block:height:12px; clear: both;">&nbsp;</div>
					<!-- DATA OBJECTS -->
				<fieldset>
					<legend style="margin-bottom:0px;"><?php echo lang('sl_data_objects'); ?></legend>
					<div class="help-inline">
						<?php echo form_dropdown('data_object_select',$characters_list,'' ,'',' id="data_object_select"'); ?>
						<span class="help-inline">
							<a class="btn btn-small" href="#" id="add_data_object">
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
					<tbody>
					<tr><td>&nbsp;</td></tr>
					</tbody>
					</table>
				</fieldset>
				<div style="display:block:height:12px; clear: both;">&nbsp;</div>
					<!-- ARTICLES -->
				<fieldset>
					<legend style="margin-bottom:0px;"><?php echo lang('sl_articles'); ?>
						<div class="help-inline right">
							<a class="btn btn-small" href="<?php echo site_url(SITE_AREA.'/custom/storylines/articles/create/'.$storyline->id); ?>" id="add_article">
								<i class="icon-plus"></i> <?php echo lang('sl_add_article'); ?>
							</a>
						</div></legend>

					<table class="table table-striped table-bordered" id="articles">
					<thead>
					<tr>
						<th class="column-check"><input class="check-all" type="checkbox" /></th>
						<th width="65%"><?php echo lang('sl_title'); ?></th>
						<th><?php echo lang('sl_actions'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php 
					if (isset($articles) && is_array($articles) && count($articles)) : ?>
					<?php	
						echo draw_articles($articles);
					?>
					<?php
					else: ?>
					<tr>
						<td colspan="3"><?php echo lang('sl_no_articles'); ?></td>
					</tr>
					<?php
					endif;
					?>
					</tbody>
					</table>
				</fieldset>
				<div style="display:block:height:12px; clear: both;">&nbsp;</div>
					<!-- CONDITIONS -->
				<fieldset>
					<legend style="margin-bottom:0px;"><?php echo lang('sl_conditions'); ?>
						<div class="help-inline right">
							<a href="#" class="btn btn-small" id="edit_conditions"><i class="icon-pencil"></i> Edit Conditions</a>
						</div>
					</legend>
					<div id="cond_waitload" class="well center" style="display:none;">
						<img src="<?php echo(TEMPLATE::theme_url('images/ajax-loader.gif'));?>" width="28" height="28" border="0" align="absmiddle" /><br />Operation in progress. Please wait...
					</div>
					<table class="table table-striped table-bordered" id="conditions_list_table">
					<thead>
					<tr>
						<th><?php echo lang('sl_category'); ?></th>
						<th width="75%"><?php echo lang('sl_condition'); ?></th>
						<th><?php echo lang('sl_value'); ?></th>
					</tr>
					</thead>
					<tbody>
					<tr><td>&times;</td></tr>
					</tbody>
					</table>

				</fieldset>

				<?php if (isset($comment_form) && !empty($comment_form)) : ?>
				<div style="display:block:height:12px; clear: both;">&nbsp;</div>
				<!-- COMMENTS -->
				<fieldset>
					<legend style="margin-bottom:0px;"><?php echo lang('sl_comments'); ?></legend>
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
						echo form_dropdown('category_id',$categories,(isset($storyline) ? $storyline->category_id : set_value('category_id')));
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
				<!--div class="control-group">
					<div class="controls">
						<?php $checked = (isset($unique_status) && $unique_status == 1) ? 'checked="checked"' : ''; ?>
						<input type="checkbox" name="use_only_once" value="1"<?php echo $checked; ?> /> <?php echo lang('sl_use_once') ?>
					</div>
				</div-->
					<!-- frequencies -->
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
					<td>
						<div id="triggers_list"></div>
					</td>
				</tr>
				<?php if (isset($triggers_list))
				{
				?>
				<tr>
					<td>
						<?php
							echo form_dropdown('trigger_select',$triggers_list,'','','class="span3" id="triggers"');
						?>
						<a class="btn btn-small" id="add_trigger"><i class="icon-plus">&times;</i>Add</a>
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
					<?php echo anchor('/users/profile/'.$storyline->created_by,find_author_name($storyline->created_by)); ?></td>
				</tr>
				<?php endif;
				if (isset($storyline->modified_on) && !empty($storyline->modified_on)) : ?>		
				<tr>
					<td>Modified</td>
					<td><?php echo date('m/d/Y h:i:s', $storyline->modified_on) .'<div class="help-inline">('. anchor(SITE_AREA.'/custom/storylines/history/1/'.$storyline->id,'History') .')</div>'; ?></td>
				</tr>
				<?php endif; ?>		
				
				</table>
			</fieldset>
		</div>
	</div>
</div>
<?php echo form_close(); ?>