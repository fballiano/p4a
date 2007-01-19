{content}

{open_javascript:h}

var rte = new FCKeditor('{id}input', '{width}', '{height}', '{theme}');
rte.BasePath = '{theme_path}/widgets/rich_textarea/';
rte.ToolbarSet = '{theme}';

rte.Config['CustomConfigurationsPath'] = '{theme_path}/widgets/rich_textarea/p4aconfig.js';
rte.Config['DefaultLanguage'] = '{language}';
{if:upload}
rte.Config['LinkBrowserURL'] = rte.BasePath + 'editor/filemanager/browser/default/browser.html?Connector=connectors/php/connector.php?p4a_application_path={application_path}&p4a_object_id={id}';
rte.Config['ImageBrowserURL'] = rte.Config['LinkBrowserURL'];
rte.Config['FlashBrowserURL'] = rte.Config['LinkBrowserURL'];
{else:}
rte.Config['LinkBrowser'] = false;
rte.Config['ImageBrowser'] = false;
rte.Config['FlashBrowser'] = false;
{end:}

rte.ReplaceTextarea();

{close_javascript:h}