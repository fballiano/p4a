<?php
define("P4A_DSN", 'mysql://root:@localhost/p4a_base_application');
define("P4A_EXTENDED_ERRORS",TRUE);
require_once( dirname(__FILE__) . '/../../p4a.php' );
$app =& p4a::singleton("p4a_base_application");
$app->main();
?>