<?php if (validation_errors()) : ?>
	<div class="alert alert-block alert-error fade in">
		<a class="close" data-dismiss="alert">&times;</a>
		<?php echo validation_errors(); ?>
	</div>

<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('sl_tokens_details'); ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
        <legend><?php echo lang('sl_general_header'); ?></legend>
			<!-- Slug -->
        <div class="control-group <?php echo form_error('slug') ? 'error' : '' ?>">
             <label class="control-label" for="slug"><?php echo lang('sl_slug') ?></label>
            <div class="controls">
                <input type="text" class="span8" name="slug" id="slug" value="<?php echo isset($token) ? $token->slug : set_value('slug') ?>" />
				<span class="help-inline"><?php if (form_error('slug')) echo form_error('slug'); else echo lang('sl_slug_note'); ?></span>
            </div>
        </div>

			<!-- Name -->
        <div class="control-group <?php echo form_error('name') ? 'error' : '' ?>">
             <label class="control-label" for="name"><?php echo lang('sl_name') ?></label>
            <div class="controls">
                <input type="text" class="span8" name="name" id="name" value="<?php echo (isset($token) && isset($token->name)) ? $token->name : set_value('name') ?>" />
				<span class="help-inline"><?php if (form_error('name')) echo form_error('name'); else echo lang('sl_name_note'); ?></span>
            </div>
        </div>
			<!-- CATEGORY ID -->
		<div class="control-group <?php echo form_error('category_id') ? 'error' : '' ?>">
			 <label class="control-label"><?php echo lang('sl_category') ?></label>
			<div class="controls">
				<?php
				if (isset($categories) && is_array($categories) && count($categories)) :

					$selection = ( isset ($token) && !empty($token->category_id ) ) ? (int) $token->category_id : 0;
					echo form_dropdown('category_id', $categories, $selection , ' class="chzn-select" id="category_id"');
				endif; ?>
				<?php if (form_error('category_id')) echo '<span class="help-inline">'. form_error('category_id') .'</span>'; ?>
			</div>
		</div>

		<?php
		$field = 'activate';
		if ($token->active) :
				$field = 'de'.$field;
		endif; ?>
		<div class="control-group">
			<div class="controls">
				<label>
					<input type="checkbox" name="<?php echo $field; ?>" value="1">
					<?php echo lang('sl_'.$field.'_note') ?>
				</label>
			</div>
		</div>
		
    </fieldset>

	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save') ?>" />
	</div>

</div>
<?php echo form_close(); ?>
