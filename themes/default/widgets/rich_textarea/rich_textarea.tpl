<script type="text/javascript" src="[[$tpl_path]]/fckeditor.js"></script>

[[$content]]

<script type="text/javascript">
[[$id]] = new FCKeditor("[[$id]]", "[[$width]]", "[[$height]]");
[[$id]].BasePath = "[[$tpl_path]]/";
[[$id]].Config["CustomConfigurationsPath"] = "[[$tpl_path]]/p4aconfig.js";
[[$id]].DefaultLanguage = "[[$language]]";
[[$id]].ToolbarSet = "p4a";
[[$id]].ReplaceTextarea();
</script>