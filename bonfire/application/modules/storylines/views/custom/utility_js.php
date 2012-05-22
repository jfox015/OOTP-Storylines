
//---------------------------------------------------------
//	!UTILITY FUNCS
//---------------------------------------------------------
var modal_mode = 'condition';
/*
	Method:
		ajax_post
	
	performs and ajax call to the server to either add or remove a data object.
	
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
/*
	Method:
		ajax_load
	
	Performs and ajax call to the server to either load a set of data results.
	
	Parameters:
		type 	-	The type of data set ot load (trigger or data object)
		
	Return
		TRUE on success, FALSE on error
*/
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
/*
	Method:
		handle_ajax_reponse
	
	Parses the response of the ajax call and routes the following actions based on a successful or 
	failed results.
	
	Parameters:
		status 	-	The Ajax status string
		data 	-	JSON data object
		type 	-	The type of data set ot load (trigger or data object)
		prefix 	-	Wait load div prefix
		
	Return
		TRUE on success, FALSE on error
*/
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
				//Storyline form data returns
				if (type == 'trigger' || type == 'triggers')
					drawTriggers(data)
				else if (type == 'data_objects' || type == 'data_object')
					drawDataObjects(data);
				// Modal window returns
				else if (type == 'new_condition')
					draw_new_condition(data);
				else if (type == 'existing_conditions')
					draw_condition_edit_table(data);
				else if (type == 'token_select')
					draw_token_select(data);
				else if (type == 'conditions_select')
					draw_condition_select(data);
				else if (type == 'token_list')
					draw_token_list(data);
				else if (type == 'result_list')
					draw_result_list(data);
				else if (type == 'condition_list' || type == 'article_conditions')
					draw_condition_list(data);
				else if (type == 'condition_save' || type == 'result_save')
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
function insertAtCaret(text)
{
	var txtarea = document.getElementById(selected_field);

	var scrollPos = txtarea.scrollTop;
	var strPos = 0;
	var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
	"ff" : (document.selection ? "ie" : false ) );
	if (br == "ie") {
		txtarea.focus();
		var range = document.selection.createRange();
		range.moveStart ('character', -txtarea.value.length);
		strPos = range.text.length;
	}
	else if (br == "ff") strPos = txtarea.selectionStart;

	var front = (txtarea.value).substring(0,strPos);
	var back = (txtarea.value).substring(strPos,txtarea.value.length);
	txtarea.value=front+text+back;
	strPos = strPos + text.length;
	if (br == "ie") {
		txtarea.focus();
		var range = document.selection.createRange();
		range.moveStart ('character', -txtarea.value.length);
		range.moveStart ('character', strPos);
		range.moveEnd ('character', 0);
		range.select();
	}
	else if (br == "ff") {
		txtarea.selectionStart = strPos;
		txtarea.selectionEnd = strPos;
		txtarea.focus();
	}
	txtarea.scrollTop = scrollPos;
}