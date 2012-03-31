$('#condition_modal').modal({
	keyboard: false,
	backdrop: true,
	static: true
});
$('#add_object_condition').click( function(e) {
	e.preventDefault();
    var val = $('#condition_select').val();
	if (val == '') {
		$('#condition_select').prev('.control-group').addClass('control-group-error');
		$('#condition_select').next('.help-inline').html('You must select a condition before adding');
	} else {
		if (find_condition(val) !== null) {
			$('#condition_select').prev('.control-group').addClass('control-group-error');
			$('#condition_select').next('.help-inline').html('Condition already added to this object');
		} else {
			// GET condition object
			var obj = conditions_objs[val], htmlOut = '', makeSlider = false;
			htmlOut += '<tr id="row_cond_'+ obj.id +'"><td><div class="control-group">';
			htmlOut += ' \t<label class="control-label"><a href="#" rel="tooltip" class="tooltips" title="' + obj.description + '">' + obj.name + '</a></label>';
			htmlOut += ' \t<div class="controls">';
			switch (obj.type) {
				case 1: // Value Range Slider
					htmlOut += ' \t<div id="cond_'+ obj.id +'"></div>';
				case 2: // Yes/No
					htmlOut += ' \t<input type="checkbox" value="cond_'+ obj.id + '"> ' + obj.name;
					break;
				case 3: // Options List
					htmlOut += ' \t<select id="cond_'+ obj.id + '"> ' + obj.name;
					if (obj.options.length > 0) {
						$.each(data.result.items, function(i,item){
							htmlOut += ' \t\t<option value="'+item.value+'">'+item.name+'</option>';
						});
					}
					htmlOut += ' \t</select>';
					break;
			} // END switch
			htmlOut += ' \t</div>';
			htmlOut += '</div></td>';
			htmlOut += '<td><a href="#" class="btn btn-danger btn-small" rel="delete_condition" id="cond_'+ obj.id +'"><i class="icon-remove icon-white"></i></a></td>';
			htmlOut += '</tr>';
			$('#conditions_tbody').append(htmlOut);
			if (makeSlider) {
				$('#cond_'+ obj.id).slider("option", { min : obj.value_range_min, max : obj.value_range_max } );
			} // END if
			data_objects_conditions[data_objects_conditions.length] = new Condition(obj.id, "cond_"+ obj.id);
		} // END if
	} // END if
});  // END #add_object_condition.click

$('#save_conditions').click( function(e) {
    e.preventDefault();
	
	// PREPARE JSON OBJECT FOR POST
	$.each(data_objects_conditions, function(i,item){
		
	});
	
	$('#data_object_modal').close();
});
$('a[rel=delete_condition]').click( function(e) {
    e.preventDefault();
	if (confirm("Are you sure you want to remove the selected condition?")) {
		$.each(data_objects_conditions, function(i,item){
			if (item.elem == this.id) {
				remove_condition(this.id);
				$('#row_'+item.elem).remove();
			}
		});
	}
});
function remove_condition(id) {
	var tmpList = [];
	$.each(data_objects_conditions, function(i,item){
		if (item.id != id) {
			tmpList[i] = item;
		}
	});
	data_objects_conditions = tmpList;
	return true;
}
function find_condition(condition_id)
{
	return (data_objects_conditions[condition_id]);
}
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
				break;
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
});


// ALERT USER ON PAGE UNLOAD THAT DRAFT LIST MAY NOT BE SAVED
$(window).bind('beforeunload', function(){
    if (pageChanged) {
    	return "WARNING: There are unsaved changes on this page. If you exit without saving, your changes will be lost. \n\nPress 'OK' to continue leaving this page or 'Cancel' to stop and save your changes first.";
    } else {
    	return "There may be unsaved changes on this page. Are you sure you want to exit? Click 'Cancel' to stop unload and save your changes first.";
    }
});
function Data_Object() {
    this.id = 0;
    this.slug = '';
    this.name = '';
    this.description = '';
    this.value = '';
    this.conditions = [];
}
function Condition(id, elem, value) {
	this.id = (id !== null) ? id : 0;
    this.elem = (elem !== null) ? elem : '';
    this.value = (value !== null) ? value : '';
}
function Condition_Obj(id, slug, name, description, type, min, max, options) {
    this.id = (id !== null) ? id : 0;
    this.slug = (slug !== null) ? slug : '';
    this.name = (name !== null) ? name : '';
    this.description = (description !== null) ? description : '';
    this.type = (type !== null) ? type : '';
    this.value_range_min = (min !== null) ? min : 0;
    this.value_range_max = (max !== null) ? max : 0;
	this.options = (options !== null) ? options : 0;
}
var storyline_id = <?php echo($storyline->id); ?>, data_objects = [], data_objects_conditions = [], pageChanged = false;
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