<?php
require_once( dirname(__FILE__) . '/../../p4a/p4a.php' );

// define("P4A_DSN", "mysql://root:@localhost/joint");
$p4a =& p4a::singleton("mysql_manager");
$p4a->main();

?>