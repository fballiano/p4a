<?php
require_once( dirname(__FILE__) . '/../../p4a/p4a.php' );

$p4a =& p4a::singleton("mysql_manager");
$p4a->main();

?>