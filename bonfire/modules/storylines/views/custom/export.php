<div class="container-fluid">
	<div class="row-fluid">
		<div class="span5">
			<div class="notification error" style="display:none;">
				&nsbp;
			</div>
			<h3>Select Output format:</h3>
			<div class="btn-group" data-toggle="buttons-radio">
			  <button class="btn" rel="format" id="xml">XML</button>
			  <button class="btn" rel="format" id="json">JSON</button>
			  <button class="btn" rel="format" id="sql">SQL</button>
			</div>
			<h3>Select Publishing Group(s):</h3>
			<div class="btn-group" data-toggle="buttons-checkbox">
			  <button class="btn" rel="status" id="3">Published</button>
			  <button class="btn" rel="status" id="2">In Review</button>
			  <button class="btn" rel="status" id="5">Archived</button>
			</div>
			<p>&nbsp;
			</p>
			<button class="btn btn-primary" href="#" id="btn_export">Export Now</button>
		</div><!--/span-->
		<div class="span7">
			<h3>Export Guide</h3>
			<p>
			TODO: Add export instructions here.
			</p>
		</span>
	</div>
</div>

<?php
	$url = site_url(SITE_AREA.'/custom/storylines/export/');
	$inline = <<<EOL
	$("#btn_export").click(function() {
		var err = null,
		formats = $('button[rel="format"]'),
		statuses = $('button[rel="status"]'),
		format = '', 
		status = '';
		$.each(formats, function (i, item) {
			if (item.classList.contains('active'))
				format = item.id;
		});
		if (format == '')
		{
			err = "You must select an output format.<br />";
		}

		$.each(statuses, function (i, item) {
			if (item.classList.contains('active')) {
				if (status != '') status == "|";
				status += item.id;
			}
		});
		
		if (status == '')
		{
			err += "You must select a publishing status.<br />";
		}
		
		if (err != null)
		{
			$('.notification').html(err);
			$('.notification').css('display','block');
		}
		else
		{
			document.location.href='{$url}/'+format+'/'+status;
		}
	});
EOL;

	Assets::add_js( $inline, 'inline' );
	unset ( $inline );
?>