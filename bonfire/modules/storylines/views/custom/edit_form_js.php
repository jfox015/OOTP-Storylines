
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
function Data_Object(id, elem, order, conditions) {
    this.id = (id !== null) ? id : 0;
    this.elem = (elem !== null) ? elem : '';
    this.order = (order !== null) ? order : '';
    this.conditions = (conditions !== null) ? conditions : [];
}
$("a[rel=edit_object_cond]").live('click', function(e) {
	e.preventDefault();
	conditions_selected = [];
	conditions_objs = [];
	modal_mode = 'condition';
	currDataObj = this.id;
	condition_level_type = 3;
	init_conditions_list(this.id);
	load_existing_conditions(this.id,condition_level_type);
	$('#condition_modal h3').html('Conditions Editor');
	$('#save_conditions').css('display','inline-block');
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
    if (confirm("Are you sure you want to remove the selected data object?")) {
        ajax_post('data_object', this.id, 'remove');
    }
});
$("a[rel=set_main_actor]").live('click', function(e) {
    e.preventDefault();
	$('#actor_node_'+this.id).css('display','none');
	$('#obj_waitload').css('display','block');
	data_obj = '{ "storyline_id": '+storyline_id+', "object_id": '+this.id+'}'
	$.post('<?php echo(site_url(SITE_AREA."/custom/storylines/make_main_actor")); ?>',{'object_data': data_obj}, function(data,status) {
		handle_ajax_reponse(status, data, 'main_actor', 'main_actor');
	});
});

function drawDataObjects(data) {

	//console.debug('drawDataObjects');
	//console.debug(data.result.items.length);
if (data.result.items.length > 0) {
		$('#data_objects_tbl > tbody:last').empty();
		data_objects = [];
		var outStr = '',order = 1;
		$.each(data.result.items, function(i,item){
			outStr += '<tr id ="obj_row_'+ item.id +'">' + "\n";
			outStr += '<td>' + "\n";
			outStr += '<input type="checkbox" name="checked[]" value="'+ item.id +'" />' + "\n";
			outStr += '</td>' + "\n";
			outStr += '<td>';
			if (item.main_actor && item.main_actor != 0) { 
				outStr += '<i title="<?php echo lang('sl_main_actor'); ?>" class="icon-hand-right"></i>&nbsp;';
			}
			outStr += '<a href="#" rel="tooltip" class="tooltips" title="'+ item.description +'">'+ item.name +'</a></td>' + "\n";
			outStr += '<td>'+ item.condition_count +'</td>' + "\n";
			outStr += '<td>' + "\n";
			outStr += '<a class="btn btn-small" href="#" rel="edit_object_cond" id="'+ item.id +'">' + "\n";
			outStr += '<i class="icon-edit"></i><?php echo lang('sl_edit'); ?>' + "\n";
			outStr += '</a>' + "\n";
			outStr += '<a class="btn btn-small" href="#" rel="remove_data_object" id="'+ item.id +'">' + "\n";
			outStr += '<i class="icon-remove"></i> <?php echo lang('sl_delete'); ?>' + "\n";
			outStr += '</a>' + "\n";
			if (item.main_actor == 0 && item.id < 12) {
				outStr += '<div id="actor_node_'+item.id+'" style="display:inline-block;"><a class="btn btn-small" href="#" rel="set_main_actor" id="'+ item.id +'">' + "\n";
				outStr += '<i class="icon-hand-right"></i>' + "\n";
				outStr += '</a></div>' + "\n";
			}
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
	e.preventDefault();
	conditions_selected = [];
	conditions_objs = [];
	modal_mode = 'condition';
	currDataObj = storyline_id;
	condition_level_type = 1;
	init_conditions_list('all');
	load_existing_conditions(currDataObj,condition_level_type);
	$('#condition_modal h3').html('Conditions Editor');
	$('#save_conditions').css('display','inline-block');
	$('#condition_modal').modal('show');
});

//--------------------------------------------------------
//	!PAGE INIT
//--------------------------------------------------------
var storyline_id = <?php echo($storyline->id); ?>, 
	pageChanged = false,
	data_objects = [], 
	triggers = [];
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