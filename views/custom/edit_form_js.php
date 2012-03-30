function data_object() {
    this.id = -1;
    this.slug = '';
    this.name = '';
    this.description = '';
    this.value = '';
    this.conditions = [];
   }
var storyline_id = <?php echo($storyline->id); ?>, data_objects = [];

$('#data_object_modal').modal({
	keyboard: false,
	backdrop: true,
	static: true
});
$('#submit_character').click( function(e) {
    e.preventDefault();
    $('#object_form').css('display','none');
    $('#modal_waitload').css('display','block');

    var statusClass = '', statusMess = '';
    $.post(url, {"storyline_id": storyline_id, "object_id" : $('#chatacter').val()}, function(data,status) {
    switch (status)
    {
    case 'success':
    if (data.status.indexOf(":") != -1)
    {
    var status = data.status.split(":");
    statusClass = 'alert-' + status[0];
    statusMess = status[1];
    }
	else
						{
                            drawDataObjects(data);
                            $('#data_object_modal').close();
                            }
			break;
			case 'timeout':
			statusClass = 'alert-error';
			statusMess = 'The server did not respond. Please try submitting again.';
			case 'error':
			statusClass = 'alert-error';
			statusMess = 'Ann error occured processing your request. Error:' + data;
			break;
	}
	if (statusMess != '') {
		$('div#modal_ajaxStatus').addClass(statusClass);
		$('div#modal_ajaxStatus').html(statusMess);
		$('div#modal_ajaxStatusBox').fadeIn("slow",function() { setTimeout('fadeModalStatus()',5000); });
	}
	$('#object_form').css('display','block');
	$('#modal_waitload').css('display','none');
});

// ALERT USER ON PAGE UNLOAD THAT DRAFT LIST MAY NOT BE SAVED
$(window).bind('beforeunload', function(){
    if (pageChanged) {
    	return "WARNING: There are unsaved changes on this page. If you exit without saving, your changes will be lost. \n\nPress 'OK' to continue leaving this page or 'Cancel' to stop and save your changes first.";
    } else {
    	return "There may be unsaved changes on this page. Are you sure you want to exit? Click 'Cancel' to stop unload and save your changes first.";
    }
});
function loadDataObjects() {
    var statusMess = '';
    $.getJSON("<?php echo(site_url("/content/storylines/get_data_objects/")); ?>"+storyline_id, function(data,status) {
		switch (status)
		{
			case 'success':
				if (data.status.indexOf(":") != -1)
				{
					var status = data.status.split(":");
					statusMess = status[1];
				}
				else
				{
					 drawDataObjects(data);
				}
				break;
			case 'timeout':
				statusMess = 'The server did not respond. Please try submitting again.';
				break;
			case 'error':
				statusMess = 'Ann error occured processing your request. Error:' + data;
				break;
		}
		if (statusMess != '') {
			$('div#data_objects_body').empty();
			$('div#data_objects_body').append('<tr> colspan="3">'+statusMess+'</td><tr>');
		}
	});
}
function drawDataObjects(data) {
    if (data.result.items.length > 0) {
		$('div#data_objects_body').empty();
		var outStr = '',count = 1;
		$.each(data.result.items, function(i,item){
			outStr += '<tr>' + "\n";
			outStr += '<td>' + "\n";
			outStr += '<input type="checkbox" name="checked[]" value="'+ item.id +'" />' + "\n";
			outStr += '</td>' + "\n";
			outStr += '<td><a href="#" rel="tooltip" class="tooltips" title="'+ item.description +'">'+ item.name +'</a></td>' + "\n";
			outStr += '<td>' + "\n";
			outStr += '<a class="btn btn-small" href="#" rel="object_edit" id="'+ storyline_id + '|'+ item.id +'">' + "\n";
			outStr += '<i class="icon-edit"></i><?php echo lang('sl_edit'); ?>' + "\n";
			outStr += '</a>' + "\n";
			outStr += '<a class="btn btn-small" href="#" rel="object_remove" id="'+ storyline_id + '|'+ item.id +'">' + "\n";
			outStr += '<i class=" icon-remove"></i> <?php echo lang('sl_delete'); ?>' + "\n";
			outStr += '</a>' + "\n";
			outStr += '</td>' + "\n";
			outStr += '</tr>' + "\n";
			count++;
		});
		$('div#data_objects_body').append(outStr);
		return true;
	} else {
		return false;
    }
}
function fadeModalStatus() {
    $('div#modal_ajaxStatusBox').fadeOut("normal",function() { clearTimeout(fader); $('div#modal_ajaxStatusBox').hide(); });
}