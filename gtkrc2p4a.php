<?php
if (@$argv[1]) {
	$file = $argv[1];
	$gtkrc = file_get_contents($file);
	
	preg_match("/gtk_color_scheme = \"(.*)\"/",$gtkrc,$results);
	$a = explode('\n',$results[1]);

	echo "<?php\n";
	foreach ($a as $row) {
		list($var,$value) = explode(':',$row);
		$var = 'P4A_THEMES_' . strtoupper($var);
		echo "define('$var','$value');\n";
	}
	echo "?>\n";
}


?>
