p4a_activate_menu = function ()
{
	var nav = 'menu';
	var navroot = document.getElementById(nav);
	if (navroot) {
		var lis=navroot.getElementsByTagName("li");
		for (i=0; i<lis.length; i++) {
			if (lis[i].lastChild.tagName.toLowerCase() == "ul") {
				lis[i].onmouseover = function() {
					this.lastChild.style.display = "block";
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
	$('img[@src$=.png]').ifixpng();
}

$(function () {
	p4a_png_fix();
	p4a_activate_menu();
});