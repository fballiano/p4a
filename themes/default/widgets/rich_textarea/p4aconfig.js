FCKConfig.AutoDetectLanguage = false;
FCKConfig.LinkUpload = false;
FCKConfig.LinkBrowser = false;
FCKConfig.ImageBrowser = false;
//FCKConfig.LinkBrowserURL = FCKConfig.BasePath + "filemanager/browser/default/browser.html?Connector=connectors/php/connector.php" ;
//FCKConfig.LinkUploadURL = FCKConfig.BasePath + "filemanager/upload/php/upload.php" ;
//FCKConfig.ImageBrowserURL = FCKConfig.BasePath + "filemanager/browser/default/browser.html?Type=Image&Connector=connectors/php/connector.php" ;
FCKConfig.StartupFocus = false;

FCKConfig.ToolbarSets["p4a"] = [
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['SpecialChar','Source'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript','-',
	'JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['Link','Unlink'],
	['Image','Table','Rule']
];