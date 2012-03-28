<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('sl_create_storyline'); ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
			<!-- Title -->
        <div class="control-group <?php echo form_error('title') ? 'error' : '' ?>">
             <label class="control-label" for="title"><?php echo lang('sl_title') ?></label>
            <div class="controls">
                <input type="text" class="span6" name="title" id="title" value="<?php echo set_value('title') ?>" />
				<?php if (form_error('title')) echo '<span class="help-inline">'. form_error('title') .'</span>'; ?>
            </div>
        </div>

		<!-- Category -->
		<?php echo form_dropdown('category_id',$categories,set_value('category_id'),lang('sl_category'),' class="span6" id="category_id"'); ?>
		
			<!-- Description -->
        <div class="control-group <?php echo form_error('description') ? 'error' : '' ?>">
             <label class="control-label"><?php echo lang('sl_description') ?></label>
            <div class="controls">
                <?php echo form_textarea( array( 'name' => 'description', 'id' => 'description', 'rows' => '5', 'class'=>'span6','cols' => '80', 'value' => isset($storyline) ? $storyline->description : set_value('description') ) )?>
				<?php if (form_error('description')) echo '<span class="help-inline">'. form_error('description') .'</span>'; ?>
            </div>
        </div>
		
			<!-- Edit after creating -->
		<div class="control-group <?php echo form_error('edit_after_create') ? 'error' : '' ?>">
			<label class="control-label"><?php echo lang('sl_edit_after_create') ?></label>
			<div class="controls">
				<?php
				echo form_checkbox('edit_after_create',1, set_value('edit_after_create'),'id="edit_after_create"');
				?>
				<span class="help-inline"><?php if (form_error('edit_after_create')) echo form_error('edit_after_create'); ?></span>
			</div>
		</div>
		
	</fieldset>
	
	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save') .' '. lang('sl_storyline') ?>" />
	</div>
	
<?php echo form_close(); ?>