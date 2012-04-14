<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('sl_create_article'); ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
		<legend><?php echo lang('sl_general_header'); ?></legend>
		<span><?php echo lang('sl_general_note'); ?>
			<!-- Title -->
        <div class="control-group <?php echo form_error('title') ? 'error' : '' ?>">
             <label class="control-label" for="title"><?php echo lang('sl_title') ?></label>
            <div class="controls">
                <input type="text" class="span6" name="title" id="title" value="<?php echo set_value('title') ?>" />
				<?php if (form_error('title')) echo '<span class="help-inline">'. form_error('title') .'</span>'; ?>
            </div>
        </div>

		<!-- Description -->
        <div class="control-group <?php echo form_error('description') ? 'error' : '' ?>">
             <label class="control-label"><?php echo lang('sl_description') ?></label>
            <div class="controls">
                <?php echo form_textarea( array( 'name' => 'description', 'id' => 'description', 'rows' => '5', 'class'=>'span6','cols' => '80', 'value' => isset($storyline) ? $storyline->description : set_value('description') ) )?>
				<?php if (form_error('description')) echo '<span class="help-inline">'. form_error('description') .'</span>'; ?>
            </div>
        </div>
		
		<legend><?php echo lang('sl_message_details'); ?></legend>
		
		<!-- Subject -->
        <div class="control-group <?php echo form_error('subject') ? 'error' : '' ?>">
             <label class="control-label" for="subject"><?php echo lang('sl_subject') ?></label>
            <div class="controls">
                <input type="text" class="span6" name="subject" id="subject" value="<?php echo set_value('subject') ?>" />
				<?php if (form_error('subject')) echo '<span class="help-inline">'. form_error('subject') .'</span>'; ?>
            </div>
        </div>

			<!-- Article Text -->
        <div class="control-group <?php echo form_error('text') ? 'error' : '' ?>">
             <label class="control-label"><?php echo lang('sl_text') ?></label>
            <div class="controls">
                <?php echo form_textarea( array( 'name' => 'text', 'id' => 'text', 'rows' => '5', 'class'=>'span6','cols' => '80', 'value' => isset($storyline) ? $storyline->text : set_value('text') ) )?>
				<?php if (form_error('text')) echo '<span class="help-inline">'. form_error('text') .'</span>'; ?>
            </div>
        </div>
		
		<!-- Game Message Type -->
		<?php echo form_dropdown('in_game_message',$game_message_types,set_value('in_game_message'),lang('sl_in_game_message'),' class="span6" id="in_game_message"'); ?>
		
		<legend><?php echo lang('sl_interactive_reponse'); ?></legend>
		
			<!-- Reply -->
        <div class="control-group <?php echo form_error('reply') ? 'error' : '' ?>">
             <label class="control-label" for="reply"><?php echo lang('sl_reply') ?></label>
            <div class="controls">
                <input type="text" class="span6" name="reply" id="reply" value="<?php echo set_value('reply') ?>" />
				<?php if (form_error('reply')) echo '<span class="help-inline">'. form_error('reply') .'</span>'; ?><br />
				<?php echo lang('sl_reply_note'); ?>
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
		<input type="hidden" name="storyline_id" value="<?php echo $storyline_id; ?>" />
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save') .' '. lang('sl_article') ?>" />
	</div>
	
<?php echo form_close(); ?>