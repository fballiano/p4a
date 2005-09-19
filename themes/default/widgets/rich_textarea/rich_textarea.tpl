{content}

{open_javascript:h}
{id} = new FCKeditor("{id}", "{width}", "{height}");
{id}.BasePath = "{theme_path}/widgets/rich_textarea/";
{id}.Config["CustomConfigurationsPath"] = "{theme_path}/widgets/rich_textarea/p4aconfig.js";
{id}.Config["DefaultLanguage"] = "{language}";
{id}.ToolbarSet = "p4a";

{if:file_upload}
{id}.Config["LinkBrowser"] = true;
{id}.Config["LinkBrowserURL"] = {id}.BasePath + 'editor/filemanager/browser/default/browser.html?Connector=connectors/php/connector.php&ServerPath={upload_path}';
{else:}
{id}.Config["LinkBrowser"] = false;
{end:}

{if:image_upload}
{id}.Config["ImageBrowser"] = true;
{id}.Config["ImageBrowserURL"] = {id}.BasePath + 'editor/filemanager/browser/default/browser.html?Type=Image&Connector=connectors/php/connector.php&ServerPath={upload_path}';
{else:}
{id}.Config["ImageBrowser"] = false;
{end:}

{id}.ReplaceTextarea();
{close_javascript:h}