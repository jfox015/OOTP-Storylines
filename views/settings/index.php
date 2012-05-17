<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('sl_storyline_options'); ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
        <legend><?php echo lang('sl_comments') ?></legend>
        <?php
        if (in_array('comments',module_list(true))) :
            ?>
            <!-- Enable Comments -->
            <div class="control-group <?php echo form_error('comments_enabled') ? 'error' : '' ?>">
                <label class="control-label"><?php echo lang('sl_comments_enabled') ?></label>
                <div class="controls">
                    <?php
                    $use_selection = ((isset($settings['storylines.comments_enabled']) && $settings['storylines.comments_enabled'] == 1) || !isset($settings['storylines.comments_enabled'])) ? true : false;
                    echo form_checkbox('comments_enabled',1, $use_selection, '', 'id="comments_enabled"');
                    ?>
                    <span class="help-inline"><?php if (form_error('comments_enabled')) echo form_error('comments_enabled'); else echo str_replace('[COMMENTS_URL]',site_url(SITE_AREA.'/content/comments/'),lang('sl_comments_enabled_note')); ?></span>
                </div>
            </div>
            <?php
        else:
            ?>
            <div class="well"><?php echo lang('sl_get_comments_module') ?></div>
            <?php
        endif;
        ?>
    </fieldset>

    <div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save'); ?>" />
	</div>
	
	<?php echo form_close(); ?>
</div>

<script type="text/javascript">
    head.ready(function(){
        $(document).ready(function() {

        });
    });

</script>

