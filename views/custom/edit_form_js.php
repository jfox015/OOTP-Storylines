
//---------------------------------------------------------
//	!ARTICLES
//---------------------------------------------------------
$('a[rel=delete_article]').click( function(e) {
    e.preventDefault();
	if (confirm("Are you sure you want to delete the selected article? This will also delete all results and conditions and may orphan any child articles.")) {
		document.location.href = '<?php echo(site_url(SITE_AREA."/custom/storylines/articles/delete")); ?>/'+this.id;
	}
});
//---------------------------------------------------------
//	!DATA OBJECTS
//---------------------------------------------------------
var storyline_id = <?php echo($storyline->id); ?>, 
	data_objects_conditions = [], 
	pageChanged = false,
	data_objects = [], 
	triggers = [], 
	currDataObj = null,
	condition_level_type = 1;
function Data_Object(id, elem, order, conditions) {
    this.id = (id !== null) ? id : 0;
    this.elem = (elem !== null) ? elem : '';
    this.order = (order !== null) ? order : '';
    this.conditions = (conditions !== null) ? conditions : [];
}
$("a[rel=edit_object_cond]").live('click', function(e) {
    e.preventDefault();
	currDataObj = this.id;
	condition_level_type = 3;
	init_conditions(this.id);
	$('#condition_modal').modal('show');
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

	console.debug('drawDataObjects');
	console.debug(data.result.items.length);
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
var triggers = [];
function Trigger(id) {
    this.id = (id !== null) ? id : 0;
}
$('#add_trigger').click( function(e) {
    e.preventDefault();
    var proceed = true, trigger_id = $('#triggers').val();
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
$('a[rel=remove_trigger]').live('click', function(e) {
    e.preventDefault();
    var proceed = false, trigger_id = this.id;
	if (trigger_id != null && trigger_id != '' && triggers.length > 0) {
		$.each(triggers, function(i, item) {
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
			outStr += '<div class="help help-inline">'+item.slug+' <a class="close" rel="remove_trigger" id="'+item.id+'"><i class="icon-remove">&times;</i></a></div>'+ "\n";
			triggers[i] = new Trigger(item.id);
		});
		$('div#triggers_list').append(outStr);
		return true;
	} else {
		$('div#triggers_list').append('<?php echo lang('sl_no_triggers'); ?>');
    }
}
//---------------------------------------------------------
//	!CONDITIONS
//---------------------------------------------------------

$('#edit_conditions').live('click', function(e) {
	currDataObj = storyline_id;
	condition_level_type = 1;
	init_conditions('all');
	$('#condition_modal').modal('show');
});
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
	if (type == 'triggers' || type == 'trigger')
		prefix = "trg";
	else if (type == 'data_objects' || type == 'data_object')
		prefix = "obj";
    $('#'+prefix+'_waitload').css('display','block');
	$.post('<?php echo site_url(SITE_AREA.'/custom/storylines'); ?>/' + func +'_'+type+'/', {'object_data':data_obj}, function(data,status) {
		handle_ajax_reponse (status, data, type, prefix);
	});
}
function ajax_load(type) {
    var prefix = '';
	if (type == 'triggers' || type == 'trigger')
		prefix = "trg";
	else if (type == 'data_objects' || type == 'data_object')
		prefix = "obj";
    $('#'+prefix+'_waitload').css('display','block');
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/")); ?>/get_"+type+"/"+storyline_id, function(data,status) {
		handle_ajax_reponse (status, data, type, prefix);
	});
}
function handle_ajax_reponse(status, data, type, prefix)
{
	var statusMess = '', statusClass = '';
	$('div#'+prefix+'_ajaxStatus').removeClass('alert-error');
	$('div#'+prefix+'_ajaxStatus').removeClass('alert-warning');
	$('div#'+prefix+'_ajaxStatus').removeClass('alert-success');
	$('div#'+prefix+'_ajaxStatus').html('');
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
				if (type == 'trigger' || type == 'triggers')
					drawTriggers(data)
				else if (type == 'data_objects' || type == 'data_object')
					drawDataObjects(data);
				else if (type == 'new_condition')
					draw_new_condition(data);
				else if (type == 'condition_list')
					draw_condition_list(data);
				else if (type == 'conditions_select')
					draw_condition_select(data);
				else if (type == 'condition_save')
				{
					$('#'+prefix+'_waitload').css('display','none');
					return true;
				}
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

//--------------------------------------------------------
//	!PAGE INIT
//--------------------------------------------------------
// LOAD DATA AND SET OBJECTS
ajax_load('data_objects');
ajax_load('triggers');
load_condition_list(storyline_id,1);
$('#condition_modal').modal({
	keyboard: false,
	static:true,
	background: true
});
$('#condition_modal').modal('hide');