<?php

require_once( dirname(__FILE__) . '/../../p4a.php' );

$p4a =& p4a::singleton("mysql_manager");
if (p4a_check_configuration($error)) {
	$p4a->main();
} else {
	print $error;
}

?>