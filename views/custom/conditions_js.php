
//---------------------------------------------------------
//	!CONDITIONS
//---------------------------------------------------------
function Condition(id, elem, value) {
	this.id = (id !== null) ? id : 0;
	this.value = (value !== null) ? value : '';
}
function Condition_Obj(id, slug, name, description, type_id, min, max, options) {
	this.id = (id !== null) ? id : 0;
	this.slug = (slug !== null) ? slug : '';
	this.name = (name !== null) ? name : '';
	this.description = (description !== null) ? description : '';
	this.type_id = (type_id !== null) ? type_id : '';
	this.value_range_min = (min !== null) ? min : 0;
	this.value_range_max = (max !== null) ? max : 0;
	this.options = (options !== null) ? options : 0;
}

var conditions_objs = [],
conditions_selected = [],
currDataObj = null,
condition_level_type = 1;

$('#add_object_condition').click( function(e) 
{
	e.preventDefault();
    $('#condition_select').prev('.control-group').removeClass('control-group-success');
    $('#condition_select').prev('.control-group').removeClass('control-group-warning');
    $('#condition_select').prev('.control-group').removeClass('control-group-error');
	$('#condition_select').next('.help-inline').html('');
	var val = $('#condition_select').val();
	if (val == '' || val == '0' || val == '-1') {
		$('#condition_select').prev('.control-group').addClass('control-group-error');
		$('#condition_select').next('.help-inline').html('You must select an item before adding');
	} else {
		if (modal_mode == 'token')
		{
			load_tokens_by_category(val);
		}
		else 
		{
			if (find_condition(val)) {
				$('#condition_select').prev('.control-group').addClass('control-group-error');
				$('#condition_select').next('.help-inline').html('Selection is already in list. only one instance is allowed per item.');
			} else {
				if (conditions_objs[val] == null) {
					// Construct URL. It changes based on the modals mode (condition or result)
					var url = '<?php echo(site_url(SITE_AREA."/custom/storylines/")); ?>';
					if (modal_mode == 'condition') url += '/conditions/get_condition/';
					else if (modal_mode == 'result') url += '/results/get_result/';
					$.getJSON(url+val, function(data,status) {
						handle_ajax_reponse (status, data, 'new_condition', 'modal');
					});
				} else {
					draw_new_condition(conditions_objs[val]);
				} // END if
				$('#save_conditions').removeAttr('disabled');
			}
		}
		
	} // END if
});  // END #add_object_condition.click

$('#save_conditions').click( function(e) {
    e.preventDefault();
	var elems = $('.condition_frm');
	// PREPARE JSON OBJECT FOR POST
	if (elems.length > 0)
	{
		var objs = [];
		// Update values from form
		$.each(elems, function(i, item) {
			var tmpobj = conditions_objs[item.id], value = null;
			var type = (modal_mode == "condition") ? tmpobj.type_id : tmpobj.value_type;
			// Set unchecked check box value to 0
			if (type == 2 && (item.value == null || item.value == '')) value = 0;
			// Handle positive/negative modifiers
			else if (type == 1 && $('#mod_'+item.id) && $('#neg_'+item.id).hasClass('active')) value = '-'+item.value;
			else value = item.value;

			objs.push({'id':item.id,'value':value});
		});
		
		var statusClass = '', statusMess = '', 
		data_obj = null,
		url = '<?php echo site_url(SITE_AREA.'/custom/storylines/'); ?>';
		if (modal_mode == "condition")
		{
			data_obj = JSON.stringify({"var_id" : currDataObj, "level_type": condition_level_type, "conditions": objs},true,4);
			url += '/conditions/save_object_conditions/';
		}
		else if (modal_mode == "result")
		{
			data_obj = JSON.stringify({"article_id" : currDataObj, "results": objs },true,4);
			url += '/results/save_object_results/';
		}
		$.post(url, {'post_data':data_obj}, function(data,status) {
			if (handle_ajax_reponse (status, data, 'result_save', 'modal')) {
				if (modal_mode == "condition")
				{
					if (condition_level_type == 3)
						ajax_load('data_objects');
					else
						load_condition_list(currDataObj, condition_level_type);
				}
				else
				{
					load_result_list(currDataObj);
				}
				$('#condition_modal').modal('hide');
			}
		});
	}
});
$('button[rel=modifier]').live('click', function(e) {
	e.preventDefault();
	var id = this.id.split('_')[1];
	if ($(this).hasClass('negative')) {
		$('#pos_'+id).removeClass('active');
	} else {
		$('#neg_'+id).removeClass('active');
	}
	$(this).addClass('active');
});
$('a[rel=delete_condition]').live('click', function(e) {
    e.preventDefault();
	if (confirm("Are you sure you want to remove the selected condition?")) {
		$('#row_cond_'+this.id).remove();
		var elems = $('.condition_frm');
		if (elems.length == 0)
			$('#save_conditions').attr('disabled',true);
	}
});
$('a[rel=delete_all]').live('click', function(e) {
    e.preventDefault();
	if (confirm("Are you sure you want to remove all selected conditions?")) {
		$('#conditions_table > tbody:last').empty();
		$('#save_conditions').attr('disabled',true);
	}
});
function load_existing_conditions(object_id, level)
{
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/conditions/get_conditions_list")); ?>/"+object_id+'/'+level, function(data,status) {
		handle_ajax_reponse (status, data, 'existing_conditions', 'modal');
	});
}function load_article_conditions(object_id, level)
{
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/conditions/get_conditions_list")); ?>/"+object_id+'/'+level, function(data,status) {
		handle_ajax_reponse (status, data, 'article_conditions', 'modal');
	});
}
function init_conditions_list(object_id)
{
	var categories = 'all';
	if (object_id != null)
	{
		switch(parseInt(object_id))
		{
			// PLAYERS/PERSON
			case 2:
			case 3:
			case 4:
				categories = '2';
				break;
			// OWNER
			case 5:
				categories = '7';
				break;
			// PERSONELL
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
			case 11:
				categories = '8';
				break;
			// TEAM
			case 12:
			case 13:
				categories = '3';
				break;
			// LEAGUE
			case 14:
			case 15:
			case 16:
				categories = '4';
				break;
			default:
				break;
		}
	}
	$('#save_conditions').attr('disabled','disabled');
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/conditions/load_conditions_list")); ?>/"+categories, function(data,status) {
		handle_ajax_reponse (status, data, 'conditions_select', 'cond');
	});
	
};
/*
	Method:
		remove_condition
	
	Removes a condition from the conditions list and the UI table.
	
	Parameters:
		id				- 	Int Condition ID
		
	Return
		TRUE
*/
function remove_condition(id) {
	var tmpList = [];
	$.each(conditions_selected, function(i,item){
		if (item.id != id) {
			tmpList[item.id] = item;
		}
	});
	conditions_selected = tmpList;
	return true;
};
function find_condition(condition_id)
{
	return (conditions_selected[condition_id]);
};
/*
	Method:
		draw_new_condition
	
	Draws a condition row to the table in the editor modal window. This serves to render both 
	conditions and results.
	
	Parameters:
		data				- 	JSON Data object. Expects items in data.results.items or data object.
		
	Return
		<void>
*/
function draw_new_condition(data) {
	// GET condition object
	var obj = ((data.result != null) ? data.result.items : data),
	    htmlOut = '', 
		makeSlider = false,
		condName = ((obj.name != null && obj.name != '') ? obj.name : obj.slug),
		type = (modal_mode == 'condition') ? obj.type_id : obj.value_type,
		condValue = ((obj.value != null && obj.value != '') ? obj.value : false);
	htmlOut += '<tr id="row_cond_'+ obj.id +'"><td><div class="control-group">';
	htmlOut += ' \t<div class="controls">';
	switch (parseInt(type)) {
		case 1: // Value Range Slider
			htmlOut += ' \t<label class="control-label"><a href="#" rel="tooltip" class="tooltips" data-original-title="' + obj.description + '">' + condName + '</a> '+obj.value_range_min+' - '+obj.value_range_max+'</label>';
			htmlOut += ' \t<div id="cond_slider_'+ obj.id +'"></div>';
			htmlOut += ' \t<div id="val_'+ obj.id + '" style="color:#0073EA;font-weight:bold;display:inline-block;float:left;"></div>';
			if (condName.indexOf('modifier') != -1) {
				htmlOut += '<div class="btn-group" id="mod_'+obj.id + '" style="display:inline-block;float:right;padding-left:15px;" data-toggle="buttons-radio">';
				htmlOut += ' \t\t<button class="btn active positive" rel="modifier" id="pos_'+ obj.id + '">+</button>';
				htmlOut += ' \t\t<button class="btn negative" rel="modifier" id="neg_'+ obj.id + '">-</button>';
				htmlOut += ' \t</div>';
			}
			htmlOut += '<input type="hidden" class="condition_frm" id="'+ obj.id + '" value="" />\n';
			makeSlider = true;
			break;
		case 2: // Yes/No Checkbox
			htmlOut += ' \t<input type="checkbox" class="condition_frm" id="'+ obj.id + '" value="1"';
			if (condValue !== false && condValue == 1) htmlOut += ' checked="checked"';
			htmlOut += '/> <a href="#" rel="tooltip" class="tooltips" data-original-title="' + obj.description + '">' + condName + '</a>';
			break;
		case 3: // Options List
			htmlOut += ' \t<label class="control-label"><a href="#" rel="tooltip" class="tooltips" data-original-title="' + obj.description + '">' + condName + '</a></label>';
			var optsArr = [];
			if (obj.options != null && obj.options != '' && obj.options.indexOf('|') != -1)
				optsArr = obj.options.split('|');
			if (optsArr.length > 0) {
				htmlOut += ' \t<select class="condition_frm" id="'+ obj.id + '">';
				$.each(optsArr, function(i,item){
					var items = item.split(":");
					htmlOut += ' \t\t<option value="'+items[0]+'"';
					if (condValue !== false && condValue == items[0]) htmlOut += ' selected="selected"';
					htmlOut += ' >'+items[1]+'</option>';
				});
				htmlOut += ' \t</select>';
			}
			break;
		case 4: // String (Text Area)
			htmlOut += '<label class="control-label"><a href="#" rel="tooltip" class="tooltips" data-original-title="' + obj.description + '">' + condName + '</a></label>';
			htmlOut += ' \t<input type="text" class="span4 condition_frm" id="'+ obj.id + '" value="'+ ((condValue !== false) ? condValue :"")+'" />';
			break;
	} // END switch
	htmlOut += ' \t</div>';
	htmlOut += '</div></td>';
	htmlOut += '<td><a href="#" class="btn-danger" rel="delete_condition" id="'+ obj.id +'"><i class="icon-remove icon-white"></i></a></td>';
	htmlOut += '</tr>';
	$('#conditions_table > tbody:last').append(htmlOut);
	if (makeSlider) {
		$('#cond_slider_'+ obj.id).slider({
			range: "max",
			value: ((condValue !== false) ? condValue : obj.value_range_min),
			min: obj.value_range_min,
			max: obj.value_range_max,
			slide: function( event, ui ) {
				$( "#"+ obj.id ).val( ui.value );
				$( "#val_"+ obj.id ).html( ui.value );
			}
		});
		$( "#"+ obj.id ).val(((condValue !== false) ? condValue : obj.value_range_min));
		$( "#val_"+ obj.id ).html(((condValue !== false) ? condValue : obj.value_range_min));
	} // END if
	if (conditions_objs[obj.id] == null) conditions_objs[obj.id] = obj;
	//conditions_selected[obj.id] = new Condition(obj.id);
};
function draw_condition_select(data) 
{
	var cond_select = $('#condition_select');
	if (cond_select != null) 
	{
		$('#condition_select').empty();
		var htmlOut = '';
		$.each(data.result.items, function(i,item) {
			htmlOut += '\t<optgroup label="'+item.label+'">\n';
			if (item.options != null && item.options.length > 0) {
				$.each(item.options, function(j,option) {
					htmlOut += '\t\t<option value="'+option.id+'">'+option.name+'</option>\n';
				});
			}
			htmlOut += '\t</optgroup>\n';
		});
		$('#condition_select').append(htmlOut);
	}
};
function draw_condition_edit_table(data) {
	var htmlOut = '';
	$('#conditions_table > tbody:last').empty();
	$.each(data.result.items, function(i,item) {
		draw_new_condition(item);
	});
	if (data.result.items.length > 0)
		$('#save_conditions').removeAttr('disabled');
};
function draw_condition_list(data) {
	var htmlOut = '';
	$.each(data.result.items, function(i,item) {
		condName = ((item.name != null && item.name != '') ? item.name : item.slug),
		val = '';
		htmlOut += '<tr>';
		htmlOut += '\t<td>'+item.category_name+'</td>';
		htmlOut += '\t<td>'+condName+'</td>';
		if (item.type_id == 2) val = ((item.value == 1) ? "Yes" : "No");
		else val = item.value;
		htmlOut += '\t<td>'+val+'</td>';
		htmlOut += '</tr>';
	});
	$('#conditions_list_table > tbody:last').empty();
	$('#conditions_list_table > tbody:last').append(htmlOut);
	$('#cond_waitload').css('display','none');
};
function load_condition_list(id, level)
{
	$('#cond_waitload').css('display','block');
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/conditions/get_conditions_list")); ?>/"+id+"/"+level, function(data,status) {
		handle_ajax_reponse (status, data, 'condition_list', 'cond');
	});
};
