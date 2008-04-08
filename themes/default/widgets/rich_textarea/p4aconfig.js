FCKConfig.AutoDetectLanguage = false;
FCKConfig.ToolbarCanCollapse = false;
FCKConfig.LinkUpload = false;
FCKConfig.ImageUpload = false;
FCKConfig.FlashUpload = false;
FCKConfig.CleanWordKeepsStructure = true;
FCKConfig.FontFormats = 'p;pre;address;h1;h2;h3;h4;h5;h6';
FCKConfig.BackgroundBlockerOpacity = 0;

FCKConfig.Plugins.Add('simplecommands');
FCKConfig.Plugins.Add('tablecommands');

FCKConfig.ToolbarSets['Default'] = [
	['Cut','Copy','Paste','PasteText','PasteWord','-','Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat','-','SourceSimple','FitWindow','Preview','Print','-','About'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript','-','OrderedList','UnorderedList','-','Outdent','Indent','-','FontFormat'],
	'/',
	['Link','Unlink','Anchor','-','Image','Flash','-','Table','TableInsertRowAfter','TableDeleteRows','TableInsertColumnAfter','TableDeleteColumns','TableInsertCellAfter','TableDeleteCells','TableMergeCells','TableHorizontalSplitCell','-','Rule','SpecialChar']
];

FCKConfig.ToolbarSets["Full"] = [
	['Source','DocProps','-','Save','NewPage','Preview','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['FontSize','TextColor','BGColor','-'],
	['FitWindow'],	
	['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'],
	'/',
	['Style','FontFormat','FontName','-','About'],
];

FCKConfig.CoreStyles['Underline'] = {Element: 'span', Styles: {'text-decoration': 'underline'}};
FCKConfig.CoreStyles['StrikeThrough'] = {Element: 'span', Styles: {'text-decoration': 'line-through'}};