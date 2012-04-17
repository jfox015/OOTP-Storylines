//--------------------------------------------------------
//	!RESULTS
//--------------------------------------------------------
/*
	Method:
		 load_existing_results()
	
	Draws results already added to the Result editor table
*/
function load_existing_results(id)
{
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/results/get_results_list")); ?>/"+id, function(data,status) {
		handle_ajax_reponse (status, data, 'existing_conditions', 'rslt');
	});
};
/*
	Method:
		 load_result_list()
	
	Draws results for the article to the table in the editor form
*/
function load_result_list(id)
{
	$('#rslt_waitload').css('display','block');
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/results/get_results_list")); ?>/"+ id, function(data,status) {
		handle_ajax_reponse (status, data, 'result_list', 'rslt');
	});
};
function draw_result_list(data) {
	var htmlOut = '';
	$.each(data.result.items, function(i,item) {
		condName = ((item.name != null && item.name != '') ? item.name : item.slug),
		val = '';
		htmlOut += '<tr>';
		htmlOut += '\t<td>'+item.category_name+'</td>';
		htmlOut += '\t<td>'+condName+'</td>';
		if (item.value_type == 2) val = ((item.result_value == 1) ? "Yes" : "No");
		else val = item.result_value;
		htmlOut += '\t<td>'+val+'</td>';
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
/*
	Method:
		 init_results()
	
	Populates the select box in the Result Editor Modal with the results options
*/
function init_results()
{
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/results/load_results_list")); ?>", function(data,status) {
		handle_ajax_reponse (status, data, 'conditions_select', 'modal');
	});
	
};
$('#add_successive_article').live('click', function(e) {
	e.preventDefault();
	document.location.href = '<?php echo site_url(SITE_AREA.'/custom/storylines/articles/create/'); ?>/'+storyline_id+'/'+article_id;
});
//---------------------------------------------------------
//	!CONDITIONS
//---------------------------------------------------------
$('#edit_conditions').live('click', function(e) {
	e.preventDefault();
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
load_article_conditions(article_id,2);
load_result_list(article_id);
$('#condition_modal').modal({
	keyboard: false,
	static:true,
	background: true
});
$('#condition_modal').modal('hide');