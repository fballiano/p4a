<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<title>P4A - PHP For Applications</title>
<head>
  <STYLE>
BODY { background-color: #FFFFFF; font-family: arial,helvetica,sans-serif; font-size:13px}
A { text-decoration: none; color #6666FF; }
A:visited { text-decoration: none; color: #6666AA; }
A:link { text-decoration: none; color: #6666AA; }
A:active { text-decoration: none; color: #6666AA; }
A:hover { text-decoration: none; color: #FF6666 }
OL,UL,P,BODY,TD,TR,TH,FORM,SPAN,DIV { font-family: arial,helvetica,sans-serif;color: #333333 }
H1,H2,H3,H4,H5,H6 { font-family: arial,helvetica,sans-serif }
PRE,TT { font-family: arial, courier,sans-serif }
  </STYLE>
</head>
<body>
<h1>P4A - PHP For Applications</h1>
<h2>Documentation</h2>
<ul>
<li><a href="docs">View documentation</a></li>
</ul>
<h2>Running Applications</h2>
<ul>
<?php
	$projects_dir = dirname(__FILE__) . '/applications/';
	$dhprojects = opendir($projects_dir);

	while (false !== ($filename = readdir($dhprojects))) {
		if ( $filename != '.' and $filename != '..' and $filename != '.svn' and is_dir($projects_dir . $filename)) {
			print "<li><a href='applications/$filename/'>{$filename}</a></li>\n";
		}
	}
?>
</ul>
</body>
</html>