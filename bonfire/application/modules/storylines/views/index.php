<h1 class="page-header"><?php echo lang('sl_custom_header'); ?></h1>
	
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span10">

            <ul class="nav nav-tabs" >
				<li <?php echo $filter=='' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url; ?>">All</a></li>
				<li <?php echo $filter=='category' ? 'class="active"' : ''; ?> class="dropdown">
					<a href="#" class="drodown-toggle" data-toggle="dropdown">
						By Category <?php echo isset($filter_category) ? ": $filter_category" : ''; ?>
						<b class="caret light-caret"></b>
					</a>
					<ul class="dropdown-menu">
					<?php if (isset($categories) && is_array($categories)) { foreach ($categories as $category) : ?>
						<li>
							<a href="<?php echo site_url('/storylines?filter=category&category_id='. $category->id) ?>">
								<?php echo $category->name; ?>
							</a>
						</li>
					<?php endforeach; } ?>
					</ul>
				</li>				
			</ul>
			<?php if (isset($query)) echo($query.'<br />'); ?>
			<?php echo form_open(current_url()) ;?>

			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width: 5%" class="column-check"><input class="check-all" type="checkbox" /></th>
						<th style="width: 5%"><?php echo lang('sl_id'); ?></th>
						<th style="width: 25%"><?php echo lang('sl_title'); ?></th>
						<th style="width: 5%"><?php echo lang('sl_article_count'); ?></th>
						<th style="width: 10%"><?php echo lang('sl_creator'); ?></th>
						<th style="width: 10%"><?php echo lang('sl_category'); ?></th>
					</tr>
				</thead>
				<?php if (isset($storylines) && is_array($storylines) && count($storylines)) : ?>
				<tfoot>
					<tr>
						<td colspan="8">
							<?php echo lang('bf_with_selected') ?>
							<input type="submit" name="submit" class="btn-danger" value="<?php echo lang('sl_action_flag') ?>">
							<input type="submit" name="submit" class="btn-success" value="<?php echo lang('sl_action_export') ?>">
						</td>
					</tr>
				</tfoot>
				<?php endif; ?>
				<tbody>

				<?php if (isset($storylines) && is_array($storylines) && count($storylines)) : ?>
					<?php foreach ($storylines as $storyline) : ?>
					<tr>
						<td>
							<input type="checkbox" name="checked[]" value="<?php echo $storyline->id ?>" />
						</td>
						<td><?php echo $storyline->id ?></td>
						<td>
							<a href="<?php echo site_url('/storylines/details/'. $storyline->id); ?>"><?php echo $storyline->title; ?></a>
						</td>
						<td><?php echo $storyline->article_count ?></td>
						<td><?php echo find_author_name($storyline->created_by) ?></td>
						<td><?php echo $storyline->category_name ?></td>
					</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="6"><?php echo lang('sl_no_matches_found') ?></td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
			<?php echo form_close(); ?>

			<?php echo $this->pagination->create_links(); ?>

		</div>	<!-- /main -->
		
		<div class="span2">
			<?php
			if ($logged_in) : ?>
			<div class="profile">
                <h3>Welcome <?php echo (isset($username) ? $username : '').(isset($user_name) ? $user_name : '') ?></h3>
				<ul>
					<li><a href="<?php echo site_url(SITE_AREA);?>">Admin Dashboard</a></li>
					<br />
                </ul>
				<a href="<?php echo site_url(SITE_AREA.'/custom/storylines/');?>">Edit Storylines</a> |
				<a href="<?php echo site_url('logout');?>">Logout</a>
				</ul>
			</div>
            <?php else: ?>
                <h3>Login<h3>

                <?php echo form_open('login'); ?>

                <label for="login_value"><?php echo $this->settings_lib->item('auth.login_type') == 'both' ? 'Username/Email' : ucwords($this->settings_lib->item('auth.login_type')) ?></label>
                <input type="text" name="login" id="login_value" value="<?php echo set_value('login'); ?>" tabindex="1" placeholder="<?php echo $this->settings_lib->item('auth.login_type') == 'both' ? lang('bf_username') .'/'. lang('bf_email') : ucwords($this->settings_lib->item('auth.login_type')) ?>" />

                <label for="password"><?php echo lang('bf_password'); ?></label>
                <input type="password" name="password" id="password" value="" tabindex="2" placeholder="<?php echo lang('bf_password'); ?>" />

                <?php if ($this->settings_lib->item('auth.allow_remember')) : ?>
                    <div class="small indent">
                        <input type="checkbox" name="remember_me" id="remember_me" value="1" tabindex="3" />
                        <label for="remember_me" class="remember"><?php echo lang('us_remember_note'); ?></label>
                    </div>
                    <?php endif; ?>

                <div class="submits">
                    <input type="submit" name="submit" id="submit" value="Login" tabindex="5" />&nbsp;
					<a href="<?php echo site_url('register');?>">Register</a>
                </div>

                <?php echo form_close(); ?>
			<?php endif;?>
		</div>
	</div>
</div>