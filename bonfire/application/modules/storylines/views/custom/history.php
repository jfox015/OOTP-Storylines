<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
		<h3>History</h3>
		<div style="float:right;">
			<?php
			$url = '';
			$type = '';
			switch ($type_id)
			{
				case 2:
					$url = '/articles/edit';
					$type = 'Article';
					break;
				default;
					$url = '/edit';
					$type = 'Storyline';
					break;
			}
			?>
			<a class="btn" href="<?php echo site_url(SITE_AREA.'/custom/storylines'.$url.'/'.$var_id); ?>">Return to <?php echo $type; ?></a>
		</div>
		<table class="table table-bordered table-striped">
		<thead>
		<tr>
			<th>Date</th>
			<th>User</th>
			<th>Action</th>
			<th>Value</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if (isset($history) && is_array($history) && count($history) > 0)
		{
			foreach ($history as $item)
			{
			?>
			<tr>
				<td><?php echo date('m/d/Y h:i:s A',$item->created_on); ?></td>
				<td><?php echo find_author_name($item->created_by); ?></td>
				<td><?php
				$val = '';
				if (isset($item->added) && !empty($item->added))
				{
					echo("Added");
					$val = $item->added;
				}
				else if (isset($item->removed) && !empty($item->removed))
				{
					echo("Removed");
					$val = $item->removed;
				}
				else if (isset($item->modified) && !empty($item->modified))
				{
					echo("Modified");
					$val = $item->modified;
				}
				?></td>
				<td><?php echo $val; ?></td>

			</tr>
			<?php
			}
		}
		?>
		</tbody>
		</table>
		<?php echo $this->pagination->create_links(); ?>
		</div><!--/span-->
	</div>
</div>