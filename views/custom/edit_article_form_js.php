//--------------------------------------------------------
//	!RESULTS
//--------------------------------------------------------
function load_existing_results(id)
{
	$('#cond_waitload').css('display','block');
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/results/get_results_list")); ?>/"+id, function(data,status) {
		handle_ajax_reponse (status, data, 'predecessor', 'rslt');
	});
};
function load_result_list(id)
{
	$('#cond_waitload').css('display','block');
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/results/get_results_list")); ?>/"+ id, function(data,status) {
		handle_ajax_reponse (status, data, 'condition_list', 'cond');
	});
};
function draw_result_list(data) {
	var htmlOut = '';
	$.each(data.result.items, function(i,item) {
		condName = ((item.name != null && item.name != '') ? item.name : item.slug),
		val = '';
		htmlOut += '<tr>';
		htmlOut += '\t<td>'+item.category_name+',/td>';
		htmlOut += '\t<td>'+condName+',/td>';
		if (item.type_id == 2) val = ((item.value == 1) ? "Yes" : "No");
		else val = item.value;
		htmlOut += '\t<td>'+val+',/td>';
	});
	$('#results_list_table > tbody:last').empty();
	$('#results_list_table > tbody:last').append(htmlOut);
};
$('#edit_results').live('click',function(e) {
	e.preventDefault();
	currDataObj = article_id;
	condition_level_type = -1;
	conditions_selected = [];
	conditions_objs = [];
	modal_mode = 'result';
	init_results();
	load_existing_results(currDataObj);
	$('#condition_modal h3').html('Results Editor');
	$('#condition_modal').modal('show');
});
function init_results()
{
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/results/load_results_list")); ?>", function(data,status) {
		handle_ajax_reponse (status, data, 'conditions_select', 'modal');
	});
	
};
//---------------------------------------------------------
//	!CONDITIONS
//---------------------------------------------------------
$('#edit_conditions').live('click', function(e) {
	conditions_selected = [];
	conditions_objs = [];
	modal_mode = 'condition';
	currDataObj = article_id;
	condition_level_type = 2;
	init_conditions_list('all');
	load_existing_conditions(currDataObj,condition_level_type);
	$('#condition_modal h3').html('Conditions Editor');
	$('#condition_modal').modal('show');
});
//--------------------------------------------------------
//	!PAGE INIT
//--------------------------------------------------------
// LOAD DATA AND SET OBJECTS
var article_id = <?php echo ($article->id); ?>,
	storyline_id = <?php echo ($article->storyline_id); ?>,
	pageChanged = false;
load_existing_conditions(article_id,2);
load_result_list(article_id);
$('#condition_modal').modal({
	keyboard: false,
	static:true,
	background: true
});
$('#condition_modal').modal('hide');