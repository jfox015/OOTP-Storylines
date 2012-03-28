<ul class="nav nav-pills">
	<li <?php echo $this->uri->segment(4) == '' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/custom/storylines') ?>"><?php echo "Index"; ?></a>
	</li>
	<li <?php echo $this->uri->segment(4) == 'create' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/custom/storylines/create') ?>" id="create_new"><?php echo 'Create'; ?></a>
	</li>
	<li <?php echo $this->uri->segment(4) == 'export' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/custom/storylines/export') ?>" id="export"><?php echo "Export"; ?></a>
	</li>
	<li <?php echo $this->uri->segment(4) == 'manage' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/custom/storylines/manage') ?>" id="manage"><?php echo 'Manage Values'; ?></a>
	</li>
	<li <?php echo $this->uri->segment(4) == 'reference' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/custom/storylines/reference') ?>" id="reference"><?php echo 'Help/Reference'; ?></a>
	</li>
</ul>
