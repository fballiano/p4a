<?php

//define("P4A_EXTENDED_ERRORS", TRUE);
require_once( dirname(__FILE__) . '/../../p4a.php' );

$p4a =& p4a::singleton("mysql_manager");
$check = p4a_check_configuration(dirname(__FILE__) . '/xml');

if (is_string($check)) {
	print $check;
} else {
	$p4a->main();
}

?>