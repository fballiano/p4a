<?php

define("P4A_LOCALE", "it_IT");

require_once( dirname(__FILE__) . '/../../p4a.php' );
$p4a =& p4a::singleton("cal");
$p4a->main();

?>