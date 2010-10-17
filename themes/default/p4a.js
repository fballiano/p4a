/**
 * Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * Code licensed under LGPL3 license:
 * http://www.gnu.org/licenses/lgpl.html
 */

p4a_working = true;
p4a_system_messages_timeout = null;
p4a_tooltip_timeout_id = null;
p4a_interval_id = null;

p4a_event_execute_prepare = function (object_name, action_name, param1, param2, param3, param4)
{
	p4a_working = true;
	p4a_rte_update_all_instances();

	if (!param1) param1 = "";
	if (!param2) param2 = "";
	if (!param3) param3 = "";
	if (!param4) param4 = "";

	p4a_form._object.value = object_name;
	p4a_form._action.value = action_name;
	p4a_form.param1.value = param1;
	p4a_form.param2.value = param2;
	p4a_form.param3.value = param3;
	p4a_form.param4.value = param4;
	p4a_form.UPLOAD_IDENTIFIER.value = object_name + p4a_form._action_id.value;
}

p4a_rte_update_all_instances = function ()
{
	$("form#p4a div.p4a_field_rich_textarea textarea").each(function () {
		try {
			CKEDITOR.instances[$(this).attr('id')].updateElement();
		} catch (e) {}
	});
}

p4a_event_execute = function (object_name, action_name, param1, param2, param3, param4)
{
	if (p4a_working) return false;
	p4a_event_execute_prepare(object_name, action_name, 0, param1, param2, param3, param4);
	p4a_form.target = '';
	
	if (p4a_ajax_enabled) {
		p4a_form._ajax.value = 2;
		p4a_upload_progress_enable();
		$('#p4a').ajaxSubmit({
			dataType: 'json',
			iframe: true,
			semantic: true,
			success: p4a_ajax_process_response
		});
	} else {
		p4a_form._ajax.value = 0;
		p4a_form.submit();
		p4a_working = false;
	}
}

p4a_keypressed_is_return = function (event)
{
	var characterCode = (window.event) ? event.keyCode : event.which;
	return (characterCode == 13);
}

p4a_keypressed_get = function (event)
{
	return (window.event) ? event.keyCode : event.which;
}

p4a_focus_set = function (id)
{
	if (id.length == 0) return;
	$('#'+id+'input').focus();
}

p4a_event_execute_ajax = function (object_name, action_name, param1, param2, param3, param4)
{
	if (p4a_working) return false;
	p4a_event_execute_prepare(object_name, action_name, param1, param2, param3, param4);
	p4a_form._ajax.value = 1;
	p4a_upload_progress_enable();
	$('#p4a').ajaxSubmit({
		dataType: 'json',
		iframe: true,
		semantic: true,
		success: p4a_ajax_process_response
	});
}

p4a_ajax_process_response = function (response)
{
	try {
		clearInterval(p4a_interval_id);
	} catch (e) {}
	
	try {
		p4a_form._action_id.value = response.action_id;
		for (i=0; i<response.widgets.length; i++) {
			var widget = response.widgets[i];
	   		var object_id = widget.id;
	   		var object = $('#'+object_id);
			if (object.size() > 0) {
	   			try {
	   				eval(p4a_html_entity_decode(widget.javascript_pre));
	   			} catch (e) {}
	   			
	   			if (object_id == 'p4a') {
	   				// do nothing, it's a special code
	   			} else if (object_id == 'p4a_inner_body') {
   					$("#p4a_inner_body").html(p4a_html_entity_decode(widget.html));
   				} else {
	   				object.parent().css('display', 'block').html(p4a_html_entity_decode(widget.html));
	   			}
	   			
	   			try {
	   				eval(p4a_html_entity_decode(widget.javascript_post));
	   			} catch (e) {}
	   		}
		}
		
		p4a_center_elements();
		p4a_menu_add_submenu_indicator();
		
		try {
			p4a_focus_set(response.focus_id);
		} catch (e) {}
		
		var messages = response.messages;
		if (messages.length > 0) {
			var new_messages_container = $('<div class="p4a_system_messages"><div class="p4a_system_messages_inner"></div></div>').appendTo(document.body).find('div');
			for (i=0; i<messages.length; i++) {
				$('<div class="p4a_system_message">'+p4a_html_entity_decode(messages[i])+'</div>').appendTo(new_messages_container);
			}
			p4a_messages_show();
		}
		
		if (typeof p4a_png_fix == 'function') p4a_png_fix();
		if (typeof p4a_menu_activate == 'function') p4a_menu_activate();
		p4a_working = false;
	} catch (e) {
		p4a_ajax_error();
	}
}

p4a_upload_progress_enable = function ()
{
	// starting upload progress check only if there's a file uploading
	if (p4a_upload_progress && $("#p4a input[type='file'][value!='']:enabled").length) {
		p4a_interval_id = setInterval("p4a_upload_progress_check()", 3000);
	}
}

p4a_upload_progress_check = function ()
{
	$.get($("#p4a").attr("action"), {
		"_p4a_upload_progress": $("#p4a_upload_identifier").val()
	}, function (percentage) {
		if (percentage.length > 0) {
			$("#p4a_loading_percentage").html(percentage).show();
		} else {
			$("#p4a_loading_percentage").hide();
		}
	});
}

p4a_ajax_error = function ()
{
	p4a_refresh();
}

p4a_refresh = function ()
{
	document.location = 'index.php';
}

p4a_loading_show = function ()
{
	$('#p4a_loading').show();
}

p4a_loading_hide = function ()
{
	$('#p4a_loading').hide();
	$("#p4a_loading_percentage").hide();
}

p4a_center_elements = function () {
	var main = $('#p4a_main');
	main.css({
		float: 'left',
		width: 'auto'
	});
	main.css({
		width: main.outerWidth(),
		float: 'none'
	});
	$('#p4a_popup #p4a_main_inner_container').css({
		width: main.outerWidth()
	});
	$('.p4a_frame_anchor_center:visible').each(function() {
		$(this).css({
			width: $(this).filter(':first-child').outerWidth(),
			float: 'none',
			marginLeft: 'auto',
			marginRight: 'auto'
		});
	});
}

p4a_tooltip_show = function (widget)
{
	$("body>.p4a_tooltip").remove();
	var widget = $(widget);
	var id = widget.attr('id');
	var tooltip = id ? $('#'+id+'tooltip') : widget.find(">.p4a_tooltip");
	tooltip.clone().appendTo("body");
	tooltip = $("body>.p4a_tooltip").css({
		top: parseInt(widget.offset().top + widget.outerHeight()),
		left: parseInt(widget.offset().left),
		width: tooltip.width()
	});

	if (p4a_shadows_enabled && !tooltip.hasClass("p4a_shadow")) {
		tooltip
			.addClass("p4a_shadow")
			.append("<div class='p4a_shadow_b'></div><div class='p4a_shadow_r'></div><div class='p4a_shadow_br'></div>");
	}
	
	tooltip.show();
	
	if ((tooltip.offset().left + tooltip.outerWidth()) > ($(window).width() + $(window).scrollLeft())) {
		tooltip.css({
			left: 'auto',
			right: 0
		});
	}
	
	if ((tooltip.offset().top + tooltip.outerHeight()) > ($(window).height() + $(window).scrollTop())) {
		tooltip.css({
			top: parseInt(widget.offset().top - tooltip.outerHeight())
		});
	}
	
	if (tooltip.bgiframe) tooltip.bgiframe();
	widget.mouseout(function() {p4a_tooltip_timeout_id = setTimeout(function () {tooltip.hide()}, 200)});
	tooltip
		.mouseover(function () {clearTimeout(p4a_tooltip_timeout_id)})
		.mouseout(function () {p4a_tooltip_timeout_id = setTimeout(function () {tooltip.hide()}, 200)});
}

p4a_tabs_load = function ()
{
	p4a_load_css(p4a_theme_path + '/jquery/ui.tabs.css');
	$(".p4a_tab_pane>ul li").hover(function () {
		$(this).addClass("ui-state-hover");
	}, function () {
		$(this).removeClass("ui-state-hover");
	});
}

p4a_autocomplete_load = function (callback)
{
	p4a_load_js(p4a_theme_path + '/jquery/ui.widget.js', function () {
		p4a_load_js(p4a_theme_path + '/jquery/ui.position.js', function () {
			p4a_load_js(p4a_theme_path + '/jquery/ui.autocomplete.js', function () {
				p4a_load_css(p4a_theme_path + '/jquery/ui.autocomplete.css', callback);
			});
		});
	});
}

p4a_calendar_load = function ()
{
	p4a_load_css(p4a_theme_path + '/jquery/ui.datepicker.css');
	p4a_load_js(p4a_theme_path + '/jquery/ui.core.js', function () {
		p4a_load_js(p4a_theme_path + '/jquery/ui.datepicker.js');
	});
}

p4a_calendar_open = function (id, options)
{
	var element = $('#'+id);
	element.datepicker('destroy');
	options.changeMonth = true;
	options.changeYear = true;
	options.dateFormat = "yy-mm-dd";
	options.dayNamesMin = p4a_calendar_daynamesmin;
	options.monthNamesShort = p4a_calendar_monthnames;
	options.firstDay = p4a_calendar_firstday;
	element.datepicker(options);
	element.datepicker('show');
	return false;
}

p4a_calendar_select = function (value_id, description_id)
{
	$.get(
		p4a_form.action,
		{_p4a_date_format: $('#'+value_id).attr('value')},
		function (new_value) {
			$('#'+description_id).attr('value', new_value).change();
		}
	);
}

p4a_maskedinput = function (id, mask)
{
	p4a_load_js(p4a_theme_path + '/jquery/maskedinput.js', function () {$('#'+id+'input').mask(mask)});
}

p4a_db_navigator_load = function (obj_id, current_id, field_to_update, roots_movement)
{
	p4a_load_js(p4a_theme_path + '/jquery/ui.core.js', function () {
		p4a_load_js(p4a_theme_path + '/jquery/ui.widget.js', function () {
			p4a_load_js(p4a_theme_path + '/jquery/ui.mouse.js', function () {
				p4a_load_js(p4a_theme_path + '/jquery/ui.draggable.js', function () {
					p4a_load_js(p4a_theme_path + '/jquery/ui.droppable.js', function () {
						p4a_db_navigator_init(obj_id, current_id, field_to_update, roots_movement);
					});
				});
			});
		});
	});
}

p4a_db_navigator_init = function (obj_id, current_id, field_to_update, roots_movement)
{
	if ($('#' + obj_id + ' li.home_node').length) {
		var is_root = $('#' + obj_id + ' li.home_node li #' + obj_id + '_' + current_id).length ? false : true;
	} else {
		var is_root = $('#' + obj_id + ' li #' + obj_id + '_' + current_id).length ? false : true;
	}
	
	if (is_root && !roots_movement) return;
	
	$('#' + obj_id + '_' + current_id).draggable({
		revert: true,
		helper: 'clone',
		start: function () {
			$(this).css('visibility', 'hidden');
		},
		stop: function () {
			$(this).css('visibility', 'visible');
		}
	});
	$('#' + obj_id + ' li a').droppable({
		accept: '.active_node',
		hoverClass: 'hoverclass',
		tolerance: 'pointer',
		drop: function() {
			$('#' + field_to_update + 'input').val($(this).parent().attr('id').split('_')[1]);
			p4a_event_execute_ajax(field_to_update, 'onchange');
		}
	});
	
	if ($('#' + obj_id + ' li.home_node').length) {
		$('#' + obj_id).droppable({
			accept: '.active_node',
			hoverClass: 'hoverclass',
			tolerance: 'pointer',
			drop: function() {
				$('#' + field_to_update + 'input').val('');
				p4a_event_execute_ajax(field_to_update, 'onChange');
			}
		});
	}
}

p4a_menu_add_submenu_indicator = function ()
{
	var submenu_indicator_html = "<span style='float:right' class='AAA'>&#x25BA;</span>";
	$('.p4a_menu_has_items .p4a_menu_has_items>a:not(.p4a_processed)')
		.addClass('p4a_processed')
		.prepend(submenu_indicator_html);
	$('.p4a_menu_has_items .p4a_menu_has_items>div:not(.p4a_processed)')
		.addClass('p4a_processed')
		.prepend(submenu_indicator_html);
}

p4a_messages_show = function ()
{
	if ($('.p4a_system_messages:visible').size() > 0) return false;
	var p4a_system_messages = $('.p4a_system_messages:hidden:first');
	if (p4a_system_messages.children().children().size() == 0) {
		p4a_system_messages.remove();
		return false;
	}
	var left = ($(window).width() - p4a_system_messages.outerWidth()) / 2;
	
	if (p4a_shadows_enabled) {
		p4a_system_messages
			.addClass("p4a_shadow")
			.append("<div class='p4a_shadow_b'></div><div class='p4a_shadow_r'></div><div class='p4a_shadow_br'></div>");
	}

	p4a_system_messages
		.css('top', $(window).scrollTop() + 20)
		.css('left', left)
		.fadeIn('normal');
	
	if (p4a_system_messages.bgiframe) {
		p4a_system_messages
			.bgiframe()
			.ifixpng();
	}
	
	p4a_messages_start_timer(2000);
	$('.p4a_system_messages:visible').mouseover(function () {
		clearTimeout(p4a_system_messages_timeout);
	}).mouseout(p4a_messages_start_timer);
}

p4a_messages_start_timer = function (milliseconds)
{
	if (typeof milliseconds != "number") milliseconds = 0;
	p4a_system_messages_timeout = setTimeout(function () {
		$('.p4a_system_messages:visible').fadeOut('normal', function() {
			$(this).hide().remove();
			p4a_messages_show();
		});
	}, milliseconds);
}

p4a_load_js = function (url, callback)
{
	if ($("script").filter(function () {return $(this).attr("src") == url}).length) {
		if (typeof callback == "function") callback();
		return;
	}
	
	var tag = document.createElement('script');
	tag.type = "text/javascript";
	tag.src = url;
	if (typeof callback != "undefined") {
		$(tag).bind('load', callback);
		tag.onreadystatechange = function() {
			if (this.readyState == 'loaded' || this.readyState == 'complete') callback();
		}
	}
	$('head').get(0).appendChild(tag);
}

p4a_load_css = function (url, callback)
{
	if ($("link").filter(function () {return $(this).attr("href") == url}).length) {
		if (typeof callback == "function") callback();
		return;
	}
	
	var tag = document.createElement('link');
	tag.type = "text/css";
	tag.rel = "stylesheet";
	tag.media = "all";
	tag.href = url;
	$('head').get(0).appendChild(tag);
	if (typeof callback == "function") callback();
}

p4a_html_entity_decode = function (string)
{
	var tmp = $("<div/>")
	var decoded = tmp.html(string).text();
	tmp.remove();
	return decoded;
}

$(function () {
	p4a_center_elements();
	p4a_form = $('#p4a')[0];
	$(document)
		.ajaxStart(p4a_loading_show)
		.ajaxStop(p4a_loading_hide)
		.ajaxError(p4a_ajax_error);
	p4a_menu_add_submenu_indicator();
	p4a_messages_show();
	if (typeof p4a_png_fix == 'function') p4a_png_fix();
	if (typeof p4a_menu_activate == 'function') p4a_menu_activate();
	setTimeout(p4a_loading_hide, 1000);
	p4a_working = false;
});