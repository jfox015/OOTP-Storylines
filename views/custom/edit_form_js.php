//---------------------------------------------------------
//	!CONDITIONS
//---------------------------------------------------------
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
	if (data_objects_conditions.length > 0) 
	{
		var statusClass = '', statusMess = '', data_obj = {"storyline_id": currStroyline, "object_id" : currDataObj, conditions: JSON.stringify(data_objects_conditions, null, 4)};
		$.post('<?php echo site_url(SITE_AREA.'/custom/storylines/conditions/save_object_conditions/'); ?>'+ object_id, {'cond_data_data':data_obj}, function(data,status) {
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
				$('div#obj_ajaxStatus').addClass(statusClass);
				$('div#obj_ajaxStatus').html(statusMess);
				$('div#obj_ajaxStatusBox').fadeIn("slow",function() { setTimeout('fadeStatus("obj_ajaxStatusBox")',5000); });
			}
			$('#obj_waitload').css('display','none');
		});
	}
	$('#data_object_modal').close();
	loadDataObjects();
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
/*
	Method:
		remove_condition
	
	Removes a condition from the conditions list and the UI table.
	
	Parameters:
		id				- 	Int Condition ID
		
	Return
		TRUE on success, FALSE on error
*/
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
//---------------------------------------------------------
//	!DATA OBJECTS
//---------------------------------------------------------
var storyline_id = <?php echo($storyline->id); ?>, 
	data_objects_conditions = [], 
	pageChanged = false,
	data_objects = [], 
	triggers = [], 
	currDataObj = null;
function Data_Object(id, elem, order, conditions) {
    this.id = (id !== null) ? id : 0;
    this.elem = (elem !== null) ? elem : '';
    this.order = (order !== null) ? order : '';
    this.conditions = (conditions !== null) ? conditions : [];
}
$("a[rel=edit_object_cond]").live('click', function(e) {
    e.preventDefault();
	var dataStr = this.id.split("|");
	currStoryline = dataStr[0];
	currDataObj = dataStr[1];
	$('#data_object_modal').modal({
		keyboard: false,
		static:true,
		background: true
	});
});
$('#add_data_object').click( function(e) {
    e.preventDefault();
    var proceed = true, data_object_id = $('#data_object_select').val();
	if (data_object_id != null && data_object_id != '' && data_objects.length > 0) {
		$.each(data_objects, function(i, item) {
			if (item.id == data_object_id)
				proceed = false;
		});
	}
	if (proceed)
	{	
		ajax_post('data_object', data_object_id, 'add');
	} else {
		$('div#obj_ajaxStatus').addClass('alert-error');
		$('div#obj_ajaxStatus').html('The object you selected has already been added to this storyline.');
		$('div#obj_ajaxStatusBox').fadeIn("slow",function() { setTimeout('fadeStatus("obj_ajaxStatusBox")',5000); });
	}
});
$("a[rel=remove_data_object]").live('click', function(e) {
    e.preventDefault();
    var proceed = false, dataStr = this.id.split("|");
	var data_object_id = dataStr[1];
	console.log(data_object_id);
	if (data_object_id != null && data_object_id != '' && data_objects.length > 0) {
		$.each(data_objects, function(i, item) {
			if (item.id == data_object_id)
				proceed = true;
		});
	}
	if (proceed)
	{	
		if (confirm("Are you sure you want to remove the selected data object?")) {
			ajax_post('data_object', data_object_id, 'remove');
		}
	} else {
		$('div#obj_ajaxStatus').addClass('alert-error');
		$('div#obj_ajaxStatus').html('The object you selected was not found in the object list. Please reload the page and try again');
		$('div#obj_ajaxStatusBox').fadeIn("slow",function() { setTimeout('fadeStatus("obj_ajaxStatusBox")',5000); });
	}
});
function drawDataObjects(data) {

	if (data.result.items.length > 0) {
		$('#data_objects_tbl > tbody:last').empty();
		data_objects = [];
		var outStr = '',order = 1;
		$.each(data.result.items, function(i,item){
			outStr += '<tr id ="obj_row_'+ item.id +'">' + "\n";
			outStr += '<td>' + "\n";
			outStr += '<input type="checkbox" name="checked[]" value="'+ item.id +'" />' + "\n";
			outStr += '</td>' + "\n";
			outStr += '<td><a href="#" rel="tooltip" class="tooltips" title="'+ item.description +'">'+ item.name +'</a></td>' + "\n";
			outStr += '<td>'+ item.condition_count +'</td>' + "\n";
			outStr += '<td>' + "\n";
			outStr += '<a class="btn btn-small" href="#" rel="edit_object_cond" id="'+ storyline_id + '|'+ item.id +'">' + "\n";
			outStr += '<i class="icon-edit"></i><?php echo lang('sl_edit'); ?>' + "\n";
			outStr += '</a>' + "\n";
			outStr += '<a class="btn btn-small" href="#" rel="remove_data_object" id="'+ storyline_id + '|'+ item.id +'">' + "\n";
			outStr += '<i class=" icon-remove"></i> <?php echo lang('sl_delete'); ?>' + "\n";
			outStr += '</a>' + "\n";
			outStr += '</td>' + "\n";
			outStr += '</tr>' + "\n";
			
			var itemOrder = 0, elem = 'obj_row_'+ item.id;
			
			if (item.slug.indexOf('LEAGUE') == -1  && item.slug != 'TEAM')
			{
				itemOrder = order++;
			}
			data_objects[i] = new Data_Object(item.id, elem, itemOrder, item.conditions);
		});
		$('#data_objects_tbl > tbody:last').append(outStr);
		return true;
	} else {
		$('#data_objects_tbl > tbody:last').append('<td colspan="4"><?php echo lang('sl_no_objects'); ?></td>');
    }
}
//---------------------------------------------------------
//	!TRIGGERS
//---------------------------------------------------------
function Trigger(id) {
    this.id = (id !== null) ? id : 0;
}
$('#add_trigger').click( function(e) {
    e.preventDefault();
    var proceed = true, trigger_id = $('#trigger_select').val();
	if (trigger_id != null && trigger_id != '' && triggers.length > 0) {
		$.each(triggers, function(i, item) {
			if (item.id == trigger_id)
				proceed = false;
		});
	}
	if (proceed)
	{	
		ajax_post('trigger', trigger_id, 'add');
	} else {
		$('div#trg_ajaxStatus').addClass('alert-error');
		$('div#trg_ajaxStatus').html('The trigger you selected has already been added to this storyline.');
		$('div#trg_ajaxStatusBox').fadeIn("slow",function() { setTimeout('fadeStatus("trg_ajaxStatusBox")',5000); });
	}
});
$('a[rel=remove_trigger]').click( function(e) {
    e.preventDefault();
    var proceed = false, dataStr = this.id.split("|"), trigger_id = dataStr[1];
	if (trigger_id != null && trigger_id != '' && data_objects.length > 0) {
		$.each(data_objects, function(i, item) {
			if (item.id == trigger_id)
				proceed = true;
		});
	}
	if (proceed)
	{	
		if (confirm("Are you sure you want to remove the selected trigger?")) {
			ajax_post('trigger', trigger_id, 'remove');
		}
	} else {
		$('div#trg_ajaxStatus').addClass('alert-error');
		$('div#trg_ajaxStatus').html('The trigger you selected was not found in the object list. Please reload the page and try again');
		$('div#trg_ajaxStatusBox').fadeIn("slow",function() { setTimeout('fadeStatus("obj_ajaxStatusBox")',5000); });
	}
});
function drawTriggers(data) {
    if (data.result.items.length > 0) {
		$('div#triggers_list').empty();
		triggers = [];
		var outStr = '',order = 1;
		$.each(data.result.items, function(i,item){
			outStr += '<div class="help help-inline">'+item.trigger_label+' <a class="close" rel="remove_trigger" id="'+item.id+'"><i class="icon-remove">&times;</i></a></div>'+ "\n";
			triggers[i] = new Trigger(item.id);
		});
		$('div#triggers_list').append(outStr);
		return true;
	} else {
		$('div#triggers_list').append('<?php echo lang('sl_no_triggers'); ?>');
    }
}
//---------------------------------------------------------
//	!UTILITY FUNCS
//---------------------------------------------------------

/*
	Method:
		ajax_post
	
	performs and ajax call to the servert to either add or remove a data object.
	
	Parameters:
		data_object_id	- 	Int Object ID
		func			-	Function to perform (add or remove)
		
	Return
		TRUE on success, FALSE on error
*/
function ajax_post(type, object_id, func ) {
	
	var prefix = '', data_obj = '{ "storyline_id": '+storyline_id+', "object_id": '+object_id+'}';
	if (type == 'triggers')
		prefix = "trg";
	else if (type == 'data_objects')
		prefix = "obj";
    $('#'+prefix+'_waitload').css('display','block');
	$.post('<?php echo site_url(SITE_AREA.'/custom/storylines'); ?>/' + func +'_'+type+'/', {'object_data':data_obj}, function(data,status) {
		handle_ajax_reponse (status, data, type, prefix);
	});
}
function ajax_load(type) {
    var prefix = '';
	if (type == 'triggers')
		prefix = "trg";
	else if (type == 'data_objects')
		prefix = "obj";
    $('#'+prefix+'_waitload').css('display','block');
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/")); ?>/get_"+type+"/"+storyline_id, function(data,status) {
		handle_ajax_reponse (status, data, type, prefix);
	});
}
function handle_ajax_reponse(status, data, type, prefix)
{
	var statusMess = '', statusClass = '';
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
				if (type == 'trigger')
					drawTriggers(data)
				else if (type == 'data_objects')
					drawDataObjects(data);
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
		$('div#'+prefix+'_ajaxStatus').addClass(statusClass);
		$('div#'+prefix+'_ajaxStatus').html(statusMess);
		$('div#'+prefix+'_ajaxStatusBox').fadeIn("slow",function() { setTimeout("fadeStatus('"+prefix+"_ajaxStatusBox')",5000); });
	}
	$('#'+prefix+'_waitload').css('display','none');
}
// ALERT USER ON PAGE UNLOAD THAT DRAFT LIST MAY NOT BE SAVED
$(window).bind('beforeunload', function(){
    if (pageChanged) {
    	return "WARNING: There are unsaved changes on this page. If you exit without saving, your changes will be lost. \n\nPress 'OK' to continue leaving this page or 'Cancel' to stop and save your changes first.";
    } else {
    	return "There may be unsaved changes on this page. Are you sure you want to exit? Click 'Cancel' to stop unload and save your changes first.";
    }
});
function fadeStatus(div) {
    if (div == null || div == '') div = 'ajaxStatusBox';
	$('div#'+div).fadeOut("normal",function() { clearTimeout(fader); $('div#'+div).hide(); });
}
function fadeModalStatus() {
    $('div#modal_ajaxStatusBox').fadeOut("normal",function() { clearTimeout(fader); $('div#modal_ajaxStatusBox').hide(); });
}
ajax_load('data_objects');