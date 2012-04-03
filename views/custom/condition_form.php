<?php if (validation_errors()) : ?>
	<div class="alert alert-block alert-error fade in">
		<a class="close" data-dismiss="alert">&times;</a>
		<?php echo validation_errors(); ?>
	</div>

<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('sl_conditions_details'); ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
        <legend>General Details</legend>
			<!-- Slug -->
        <div class="control-group <?php echo form_error('slug') ? 'error' : '' ?>">
             <label class="control-label" for="slug"><?php echo lang('sl_slug') ?></label>
            <div class="controls">
                <input type="text" class="span8" name="slug" id="slug" value="<?php echo isset($condition) ? $condition->slug : set_value('slug') ?>" />
				<span class="help-inline"><?php if (form_error('slug')) echo form_error('slug'); else echo lang('sl_slug_note'); ?></span>
            </div>
        </div>

			<!-- Name -->
        <div class="control-group <?php echo form_error('name') ? 'error' : '' ?>">
             <label class="control-label" for="name"><?php echo lang('sl_name') ?></label>
            <div class="controls">
                <input type="text" class="span8" name="name" id="name" value="<?php echo (isset($condition) && isset($condition->name)) ? $condition->name : set_value('name') ?>" />
				<span class="help-inline"><?php if (form_error('name')) echo form_error('name'); else echo lang('sl_name_note'); ?></span>
            </div>
        </div>

			<!-- Description -->
        <div class="control-group <?php echo form_error('description') ? 'error' : '' ?>">
             <label class="control-label"><?php echo lang('sl_description') ?></label>
            <div class="controls">
                <?php echo form_textarea( array( 'class' => 'editor', 'name' => 'description', 'class' => 'span8', 'id' => 'description', 'rows' => '8', 'cols' => '80', 'value' => isset($condition) ? $condition->description : set_value('description') ) )?>
				<span class="help-inline"><?php if (form_error('description')) echo description('name'); else echo lang('sl_description_note'); ?></span>
            </div>
        </div>
			<!-- CATEGORY ID -->
		<div class="control-group <?php echo form_error('category_id') ? 'error' : '' ?>">
			 <label class="control-label"><?php echo lang('sl_category') ?></label>
			<div class="controls">
				<?php
				if (isset($categories) && is_array($categories) && count($categories)) :

					$selection = ( isset ($condition) && !empty($condition->category_id ) ) ? (int) $condition->category_id : 0;
					echo form_dropdown('category_id', $categories, $selection , ' class="chzn-select" id="category_id"');
				endif; ?>
				<?php if (form_error('category_id')) echo '<span class="help-inline">'. form_error('category_id') .'</span>'; ?>
			</div>
		</div>
			<!-- LEVEL ID -->
		<div class="control-group <?php echo form_error('level_id') ? 'error' : '' ?>">
			 <label class="control-label"><?php echo lang('sl_level') ?></label>
			<div class="controls">
				<?php
				if (isset($levels) && is_array($levels) && count($levels)) :
					$selection = ( isset ($condition) && !empty($condition->level_id ) ) ? (int) $condition->level_id : 0;
					echo form_dropdown('level_id', $levels, $selection , ' class="chzn-select" id="level_id"');
				endif; ?>
				<?php if (form_error('level_id')) echo '<span class="help-inline">'. form_error('level_id') .'</span>'; ?>
			</div>
		</div>

		<?php
		$field = 'activate';
		if ($condition->active) :
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
		<div class="control-group <?php echo form_error('type_id') ? 'error' : '' ?>">
			 <label class="control-label"><?php echo lang('sl_type') ?></label>
			<div class="controls">
				<?php
				if (isset($levels) && is_array($levels) && count($levels)) :
					$selection = ( isset ($condition) && !empty($condition->type_id ) ) ? (int) $condition->type_id : 0;
					echo form_dropdown('type_id', $types, $selection ,' class="chzn-select" id="type_id"');
				endif; ?>
				<?php if (form_error('type_id')) echo '<span class="help-inline">'. form_error('type_id') .'</span>'; ?>
			</div>
		</div>

			<!-- VALUE RANGE -->
		<div class="control-group <?php echo form_error('value_range_min') || form_error('value_range_max') ? 'error' : '' ?>">
             <label class="control-label"><?php echo lang('us_value_range') ?></label>
            <div class="controls">
                <?php echo lang('sl_value_range_min'); ?>: <input type="text" style="width: 3em;" id="value_range_min" name="value_range_min" value="<?php echo (isset($condition) && isset($condition->value_range_min) ) ? (int)$condition->value_range_min: set_value('value_range_min'); ?>" />
                <?php if (form_error('value_range_min')) echo '<span class="help-inline">'. form_error('value_range_min') .'</span>'; ?>
                <?php echo lang('sl_value_range_max'); ?>: <input type="text" style="width: 3em;" id="value_range_max" name="value_range_max" value="<?php echo (isset($condition) && isset($condition->value_range_max) ) ? (int)$condition->value_range_max: set_value('value_range_max') ?>" />
                <?php if (form_error('value_range_max')) echo '<span class="help-inline">'. form_error('value_range_max') .'</span>'; ?>
            </div>
        </div>

			<!-- OPTIONS -->
		<div class="control-group <?php echo form_error('options') ? 'error' : '' ?>">
			<label class="control-label"><?php echo lang('sl_options') ?></label>
			<div class="controls">
				<input type="text" id="options" name="options" value="<?php echo isset($condition) ? $condition->options : set_value('options') ?>" />
				<span class="help-inline"><?php if (form_error('name')) echo form_error('name'); else echo lang('sl_options_note'); ?></span>'
            </div>
		</div>
		
	</fieldset>

	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save') ?>" />
	</div>

</div>
<?php echo form_close(); ?>
