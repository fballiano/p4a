<?php

/*
 * This is the main application file.
 * Here you can define your custom costants overriding p4a defaults.
 * Than you must call p4a.
 * That's it!
 */

// Optional - tells how to connect to the database
// define("P4A_DSN", 'protocol://username:password@host/database');
// define("P4A_DSN", 'mysql://root:@localhost/p4asample');

// Optional - tells what locale we're going to use
// define("P4A_LOCALE", 'en_US');

// Check Installation and configuration.
// This lines should be removed after the first run.
include( dirname(__FILE__) . '/../../core/libraries/check_configuration.php' );
if( !check_configuration( $error ) ) {
	die( $error );
}

// Here we go
require_once( dirname(__FILE__) . '/../../p4a/p4a.php' );

$sample =& p4a::singleton("sample"); 
$sample->main();

?>