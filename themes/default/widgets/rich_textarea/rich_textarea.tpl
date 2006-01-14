{content}

{open_javascript:h}

tinyMCE.init({
	mode: "exact",
	elements : "{id}",
	theme: "advanced",
	language: "en",
	plugins: "table,advhr,advimage,advlink,emotions,preview,flash,searchreplace,print,paste,directionality,fullscreen,noneditable,contextmenu",
	theme_advanced_buttons1: "cut,copy,paste,pastetext,pasteword,separator,undo,redo,separator,search,replace,separator,ltr,rtl,separator,charmap,separator,removeformat,cleanup,separator,code,preview,fullscreen,print,separator,help",
	theme_advanced_buttons2: "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,formatselect,separator,sub,sup,bullist,numlist,separator,outdent,indent",
	theme_advanced_buttons3: "tablecontrols,separator,,link,unlink,anchor,image,separator,visualaid,flash,advhr",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	theme_advanced_statusbar_location: "bottom",
	{if:upload}file_browser_callback: "{id}fileBrowserCallBack",{end:}
	auto_cleanup_word: true,
	relative_urls: false
});

{if:upload}
function {id}fileBrowserCallBack(field_name, url, type, win) {
	tinyfck_field = field_name;
	tinyfck = win;
	window.open("../filemanager/browser.html?Connector=connectors/php/connector.php&ServerPath={upload_path}", "{id}", "modal,width=600,height=400");
}
{end:}

{close_javascript:h}