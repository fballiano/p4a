[[$content]]

<script type="text/javascript">
[[$id]] = new FCKeditor("[[$id]]", "[[$width]]", "[[$height]]");
[[$id]].BasePath = "[[$tpl_path]]/";
[[$id]].Config["CustomConfigurationsPath"] = "[[$tpl_path]]/p4aconfig.js";
[[$id]].Config["DefaultLanguage"] = "[[$language]]";
[[$id]].ToolbarSet = "p4a";

[[if $file_upload]]
[[$id]].Config["LinkBrowser"] = true;
[[$id]].Config["LinkBrowserURL"] = [[$id]].BasePath + 'editor/filemanager/browser/default/browser.html?Connector=connectors/php/connector.php&ServerPath=[[$upload_path]]';
[[else]]
[[$id]].Config["LinkBrowser"] = false;
[[/if]]

[[if $image_upload]]
[[$id]].Config["ImageBrowser"] = true;
[[$id]].Config["ImageBrowserURL"] = [[$id]].BasePath + 'editor/filemanager/browser/default/browser.html?Type=Image&Connector=connectors/php/connector.php&ServerPath=[[$upload_path]]';
[[else]]
[[$id]].Config["ImageBrowser"] = false;
[[/if]]

[[$id]].ReplaceTextarea();
</script>