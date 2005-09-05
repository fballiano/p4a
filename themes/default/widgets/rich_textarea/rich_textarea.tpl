{content}

{_tpl_vars[open_javascript]:h}
{getId()} = new FCKeditor("{getId()}", "{getWidth()}", "{getHeight()}");
{getId()}.BasePath = "{_tpl_vars[theme_path]}/widgets/rich_textarea/";
{getId()}.Config["CustomConfigurationsPath"] = "{_tpl_vars[theme_path]}/widgets/rich_textarea/p4aconfig.js";
{getId()}.Config["DefaultLanguage"] = "{_temp_vars[language]}";
{getId()}.ToolbarSet = "p4a";

{if:file_upload}
{getId()}.Config["LinkBrowser"] = true;
{getId()}.Config["LinkBrowserURL"] = {getId()}.BasePath + 'editor/filemanager/browser/default/browser.html?Connector=connectors/php/connector.php&ServerPath={_temp_vars[upload_path]}';
{else:}
{getId()}.Config["LinkBrowser"] = false;
{end:}

{if:image_upload}
{getId()}.Config["ImageBrowser"] = true;
{getId()}.Config["ImageBrowserURL"] = {getId()}.BasePath + 'editor/filemanager/browser/default/browser.html?Type=Image&Connector=connectors/php/connector.php&ServerPath={_temp_vars[upload_path]}';
{else:}
{getId()}.Config["ImageBrowser"] = false;
{end:}

{getId()}.ReplaceTextarea();
{_tpl_vars[close_javascript]:h}