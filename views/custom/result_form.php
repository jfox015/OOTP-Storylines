<?php if (validation_errors()) : ?>
	<div class="alert alert-block alert-error fade in">
		<a class="close" data-dismiss="alert">&times;</a>
		<?php echo validation_errors(); ?>
	</div>

<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('sl_results_details'); ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
        <legend><?php echo lang('sl_general_header'); ?></legend>
			<!-- Slug -->
        <div class="control-group <?php echo form_error('slug') ? 'error' : '' ?>">
             <label class="control-label" for="slug"><?php echo lang('sl_slug') ?></label>
            <div class="controls">
                <input type="text" class="span8" name="slug" id="slug" value="<?php echo isset($result) ? $result->slug : set_value('slug') ?>" />
				<span class="help-inline"><?php if (form_error('slug')) echo form_error('slug'); else echo lang('sl_slug_note'); ?></span>
            </div>
        </div>

			<!-- Name -->
        <div class="control-group <?php echo form_error('name') ? 'error' : '' ?>">
             <label class="control-label" for="name"><?php echo lang('us_date') ?></label>
            <div class="controls">
                <input type="text" class="span8" name="name" id="name" value="<?php echo (isset($result) && isset($result->name)) ? $result->name : set_value('name') ?>" />
				<span class="help-inline"><?php if (form_error('name')) echo form_error('name'); else echo lang('sl_name_note'); ?></span>
            </div>
        </div>

			<!-- Description -->
        <div class="control-group <?php echo form_error('description') ? 'error' : '' ?>">
             <label class="control-label"><?php echo lang('us_body') ?></label>
            <div class="controls">
                <?php echo form_textarea( array( 'class' => 'editor', 'class' => "span8", 'name' => 'description', 'id' => 'description', 'rows' => '8', 'cols' => '80', 'value' => isset($result) ? $result->description : set_value('description') ) )?>
				<span class="help-inline"><?php if (form_error('description')) echo form_error('description'); else echo lang('sl_description_note'); ?></span>
            </div>
        </div>
			<!-- CATEGORY ID -->
		<?php
		if (isset($categories) && is_array($categories) && count($categories)) :

			$selection = ( isset ($result) && !empty($result->category_id ) ) ? (int) $result->category_id : 0;
			echo form_dropdown('category_id', $categories, $selection , lang('sl_category'), ' class="chzn-select" id="category_id"');
		endif; ?>

		<?php
		$field = 'activate';
		if ($result->active) :
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
    <fieldset>
	    <legend>Values</legend>

			<!-- TYPE ID -->
		<?php
		if (isset($value_types) && is_array($value_types) && count($value_types)) :
			$selection = ( isset ($result) && !empty($result->value_type ) ) ? (int) $result->value_type : 0;
			echo form_dropdown('value_type', $value_types, $selection , lang('sl_level'), ' class="chzn-select" id="value_type"');
		endif; ?>

			<!-- VALUE RANGE -->
		<div class="control-group <?php echo form_error('value_range_min') || form_error('value_range_max') ? 'error' : '' ?>">
             <label class="control-label"><?php echo lang('us_value_range') ?></label>
            <div class="controls">
                <?php echo lang('sl_value_range_min'); ?>: <input type="text" style="width: 3em;" id="value_range_min" name="value_range_min" value="<?php echo (isset($result) && isset($result->value_range_min) ) ? (int)$result->value_range_min: set_value('value_range_min'); ?>" />
                <?php if (form_error('value_range_min')) echo '<span class="help-inline">'. form_error('value_range_min') .'</span>'; ?>
                <?php echo lang('sl_value_range_max'); ?>: <input type="text" style="width: 3em;" id="value_range_max" name="value_range_max" value="<?php echo (isset($result) && isset($result->value_range_max) ) ? (int)$result->value_range_max: set_value('value_range_max') ?>" />
                <?php if (form_error('value_range_max')) echo '<span class="help-inline">'. form_error('value_range_max') .'</span>'; ?>
            </div>
        </div>

			<!-- OPTIONS -->
		<div class="control-group <?php echo form_error('options') ? 'error' : '' ?>">
			<label class="control-label"><?php echo lang('sl_options') ?></label>
			<div class="controls">
				<input type="text" class="span8" id="options" name="options" value="<?php echo isset($result) ? $result->options : set_value('options') ?>" />
				<span class="help-inline"><?php if (form_error('name')) echo form_error('name'); else echo lang('sl_options_note'); ?></span>
            </div>
		</div>

			<!-- RULES -->
		<div class="control-group <?php echo form_error('rules') ? 'error' : '' ?>">
			<label class="control-label"><?php echo lang('sl_rules') ?></label>
			<div class="controls">
				<input type="text" class="span8" id="rules" name="rules" value="<?php echo isset($result) ? $result->rules : set_value('rules') ?>" />
				<span class="help-inline"><?php if (form_error('rules')) echo form_error('rules'); else echo lang('sl_rules_note'); ?></span>
            </div>
		</div
	</fieldset>

	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save') ?>" />
	</div>

</div>
<?php echo form_close(); ?>
