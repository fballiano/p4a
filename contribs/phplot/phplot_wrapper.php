<?php

$id = $_REQUEST["id"];
define("P4A_APPLICATION_NAME", $_REQUEST["p4a_application_name"]);
require_once "{$_REQUEST['p4a_root_dir']}/p4a.php";

$p4a =& p4a::singleton();
$obj =& $p4a->getObject($id);
$obj->display();

?>