FCKConfig.DisableEnterKeyHandler = false;
FCKConfig.AutoDetectLanguage = false;
FCKConfig.ToolbarCanCollapse = false;
FCKConfig.LinkUpload = false;
FCKConfig.ImageUpload = false;
FCKConfig.FlashUpload = false;
FCKConfig.CleanWordKeepsStructure = true;
FCKConfig.FontFormats = 'p;pre;address;h1;h2;h3;h4;h5;h6';

FCKConfig.Plugins.Add('simplecommands');
FCKConfig.Plugins.Add('tablecommands');

FCKConfig.ToolbarSets['Default'] = [
	['Cut','Copy','Paste','PasteText','PasteWord','-','Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat','-','SourceSimple','FitWindow','Preview','Print','-','About'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript','-','OrderedList','UnorderedList','-','Outdent','Indent','-','FontFormat'],
	'/',
	['Link','Unlink','Anchor','-','Image','Flash','-','Table','TableInsertRow','TableDeleteRows','TableInsertColumn','TableDeleteColumns','TableInsertCell','TableDeleteCells','TableMergeCells','TableSplitCell','-','Rule','SpecialChar']
];