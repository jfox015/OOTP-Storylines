<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
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
	</div>
</div>

<?php
	$url = site_url(SITE_AREA.'/custom/storylines/export/');
	$inline = <<<EOL
	$("#btn_export").click(function() {
		var err = null,
		format = $('button[rel="format"]:checked'),
		status = $('button[rel="status"]:checked'),
		formats = '', statuses = '';
		if (format.length == 0)
		{
			err = "You must select an output format.<br />";
		} 
		if (status.length == 0)
		{
			err += "You must select a publishing status.<br />";
		} 
		else 
		{
			$.each(status, function(i, item) {
				if (statuses != '') statuses == "|";
				statuses += item.id;
			});
		}
		
		if (err != null)
		{
			$('.notification').html(err);
			$('.notification').css('display','block');
		}
		else
		{
			document.location.href='{$url}/'+format+'/'+statuses;
		}
	});
EOL;

	Assets::add_js( $inline, 'inline' );
	unset ( $inline );
?>