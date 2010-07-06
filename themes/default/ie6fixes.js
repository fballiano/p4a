/**
 * Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * Code licensed under LGPL3 license:
 * http://www.gnu.org/licenses/lgpl.html
 */

p4a_menu_activate = function ()
{
	$('#p4a_menu li').each(function () {
		$(this).hover(
			function () {
				$(this).children().show();
			},
			function () {
				$(this).find('ul').hide();
			}
		);
	});
}

p4a_png_fix = function ()
{
	$.ifixpng(p4a_theme_path + '/jquery/pixel.gif');
	$('.p4a_db_navigator li').ifixpng();
	$("img[src$=.png]").each(function () {
		var parents = jQuery.makeArray($(this).parents());
		var found = false;
		for (var i=0; i<parents.length; i++) {
			if (!$(parents[i]).is(':visible')) {
				found = true;
				break;
			}
		}
		if (!found) {
			$(this).ifixpng();
		}
	});
}