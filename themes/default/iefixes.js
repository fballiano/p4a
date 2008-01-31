p4a_menu_activate = function ()
{
	var nav = 'p4a_menu';
	var navroot = document.getElementById(nav);
	if (navroot) {
		var lis=navroot.getElementsByTagName("li");
		for (i=0; i<lis.length; i++) {
			if (lis[i].lastChild.tagName.toLowerCase() == "ul") {
				lis[i].onmouseover = function() {
					this.lastChild.style.display = "block";
					$(this).find("img[@src$=.png]").ifixpng();
				}
				lis[i].onmouseout = function() {
					this.lastChild.style.display = "none";
				}
			}
		}
	}
}

p4a_png_fix = function ()
{
	$.ifixpng(p4a_theme_path + '/jquery/pixel.gif');
	
	$("img[@src$=.png]").each(function () {
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

$(function () {
	p4a_png_fix();
	p4a_menu_activate();
});