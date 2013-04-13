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
		htmlOut += '\t</tr>';
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
	$('#save_conditions').css('display','inline-block');
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
//---------------------------------------------------------
//	!ARTICLES
//---------------------------------------------------------
$('#add_successive_article').live('click', function(e) {
	e.preventDefault();
	document.location.href = '<?php echo site_url(SITE_AREA.'/custom/storylines/articles/create/'); ?>/'+storyline_id+'/'+article_id;
});
//---------------------------------------------------------
//	!TOKENS
//---------------------------------------------------------
$('a[rel=insert_token]').live('click', function(e) {
	e.preventDefault();
	insertAtCaret('['+token_list[this.id].slug.replace(/#/,'#' + data_objects[currDataObj].object_num)+']');
	$('#condition_modal').modal('hide');
});	
$('#add_token').live('click', function(e) {
	e.preventDefault();
	var attr = $(this).attr('disabled');
	if (typeof attr === 'undefined')
	{
		if (selected_field == null) 
		{
			alert("You must select the text or subject fields to insert a token.");
		}
		else 
		{
			modal_mode = 'token';
			currDataObj = 0;
			condition_level_type = 0;
			init_token_data_objects();
			$('#condition_modal h3').html('Insert Token');
			$('#save_conditions').css('display','none');
			$('#conditions_table > tbody:last').empty();
			$('#condition_modal').modal('show');
		}
	}
	else
	{
		return;
	}
});
function init_token_data_objects()
{
	$.getJSON("<?php echo(site_url(SITE_AREA."/custom/storylines/get_data_objects_list")); ?>/"+storyline_id, function(data,status) {
		handle_ajax_reponse (status, data, 'token_select', 'modal');
	});
}
function load_tokens_by_category(object_id)
{
	//console.log(object_id);
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
		// PERSONEL
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
	currDataObj = object_id;
	//console.log(currDataObj);
	$.getJSON("<?php echo(site_url(SITE_AREA.'/custom/storylines/tokens/load_tokens_by_category/')); ?>/"+categories, function(data,status) {
		handle_ajax_reponse (status, data, 'token_list', 'modal');
	});
}
function draw_token_select(data) 
{
	var cond_select = $('#condition_select');
	if (cond_select != null) 
	{
		$('#condition_select').empty();
		var htmlOut = '';
		$.each(data.result.items, function(i,item) {
			htmlOut += '\t\t<option value="'+item.id+'">'+item.name+'</option>\n';
		});
		$('#condition_select').append(htmlOut);
	}
};
function draw_token_list(data) {
	var htmlOut = '';
	htmlOut += '<tr>';
	htmlOut += '\t<th></th>';
	htmlOut += '\t<th>Token</th>';
	htmlOut += '\t<th>Example</th>';
	htmlOut += '\t</tr>';
	$.each(data.result.items, function(i,item) {
		condName = ((item.name != null && item.name != '') ? item.name : item.slug),
		htmlOut += '<tr>';
		htmlOut += '\t<td><a class="btn" href="#" rel="insert_token" id="'+item.id+'"><i class="icon-arrow-left"></i> Insert</a></td>';
		htmlOut += '\t<td>'+item.slug+'</td>';
		htmlOut += '\t<td>'+item.name+'</td>';
		htmlOut += '\t</tr>';
		token_list[item.id] = item;
	});
	$('#conditions_table > tbody:last').empty();
	$('#conditions_table > tbody:last').append(htmlOut);
};

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
	$('#save_conditions').css('display','inline-block');
	$('#condition_modal').modal('show');
});
//---------------------------------------------------------
//	!PREVIEW
//---------------------------------------------------------
$('#text').blur(function(e) {
	var article_txt = $('#text').val(), prev = $('#preview');
	if (article_txt && article_txt.length > 0) {
		prev.removeClass('disabled');
		prev.removeAttr('disabled');
	} else {
		prev.addClass('disabled');
		prev.attr("disabled", 'disabled');
	}
});

$('#preview').click(function(e) {
	var article_txt = $('#text').val();
	//parse tokens from text body
	var re = new RegExp(/\[(.+?)\]/);
	var token_list = re.exec(article_txt);
	if (token_list.length > 0) {
		// LOAD TOKENS
		$.ajax({
			dataType: "json",
			url: '<?php echo(site_url(SITE_AREA.'/custom/storylines/tokens/load_tokens')); ?>',
			data: {'tokens': token_list},
			success: function(tokens) {
				$.each(tokens, function(item) {
					var start_str = '', end_str = '';
					if (item.slug.indexOf('link') != -1) {
						start_str = '<a href="#">';
						end_str = '</a>';
					}
                    article_txt.replace('[ ' + item.slug + ' ]',start_str + item.name + end_str);
				});
				$('#preview_content').html(article_txt);
				$('#preview_modal').modal('show');
			},
			error: function(error) {
				$('#preview_content').html(article_txt);
				$('#preview_modal').modal('show');
			}
		});
	}
});
$('#preview_modal').modal({
	keyboard: false,
	static:true,
	background: true
});
$('#preview_modal').modal('hide');
//--------------------------------------------------------
//	!PAGE INIT
//--------------------------------------------------------
// LOAD DATA AND SET OBJECTS
var article_id = <?php echo ($article->id); ?>,
	storyline_id = <?php echo ($article->storyline_id); ?>,
	pageChanged = false,
	selected_field = null,
	token_list = [];
load_article_conditions(article_id,2);
load_result_list(article_id);
$('#condition_modal').modal({
	keyboard: false,
	static:true,
	background: true
});
$('#condition_modal').modal('hide');
$(":input").focus(function () {
    //console.log(data_objects.length);
	if (data_objects.length > 0 && (this.id == "subject" || this.id == "text"))
	{
		$('#add_token').removeAttr('disabled');
		selected_field = this.id;
	}
	else
	{
		$('#add_token').attr('disabled','disabled');
		selected_field = null;
	}
});