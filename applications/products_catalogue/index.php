<?php
//define("P4A_EXTENDED_ERRORS", 'TRUE');
define("P4A_LOCALE", 'en_US');
define("P4A_DSN", 'mysql://root:@localhost/p4a_products_catalogue');

require_once( dirname(__FILE__) . '/../../p4a.php' );

// Check Installation and configuration.
// This lines should be removed after the first run.
$p4a =& p4a::singleton("products_catalogue");
$check = p4a_check_configuration();

// Here we go
if (is_string($check)) {
	print $check;
} else {
	$p4a->main();
}

?>