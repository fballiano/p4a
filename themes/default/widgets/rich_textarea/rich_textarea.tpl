<script type="text/javascript">
	_editor_url = "[[$tpl_path]]/";
	_editor_lang = "en";
</script>
<script type="text/javascript" src="[[$tpl_path]]/htmlarea.js"></script>
<script type="text/javascript">
[[if $table_operations]]
	HTMLArea.loadPlugin("TableOperations");
[[/if]]

[[if $spell_checker]]
	HTMLArea.loadPlugin("SpellChecker");
[[/if]]

[[if $contextual_menu]]
	HTMLArea.loadPlugin("ContextMenu");
[[/if]]
</script>

[[$content]]

<script type="text/javascript" defer="1">
	[[$id]] = new HTMLArea("[[$id]]");
	
[[if $table_operations]]
	[[$id]].registerPlugin(TableOperations);
[[/if]]

[[if $spell_checker]]
	[[$id]].registerPlugin(SpellChecker);
[[/if]]

[[if $contextual_menu]]
	[[$id]].registerPlugin(ContextMenu);
[[/if]]
	
    //[[$id]].generate();
    setTimeout(function() {[[$id]].generate();}, 500);
</script>