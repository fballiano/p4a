<?php

/*
This file is used as a "service file", all calls will come here,
hiding all other files that are part of the application.
Here we define all the constants that are involved in the application
start. The next step is to include the system library.
Now we can finally instance and launch the application.
*/

/*
We define the locale constant, this is for english/american
users. We also define the database connection with the
classis DSN.
To view all the available constants take a look at
p4a/config.php.
In this sample we have no database connection so simply
comment out the relative instruction.
*/

define("P4A_LOCALE", 'en_US');
//define("P4A_DSN", 'mysql://root:@localhost/sample_en');

// Including p4a
require_once dirname(__FILE__) . '/../../p4a.php';

/*
Now we instance the application with the singleton
method, than we call the "main" method. main is executed
every page call (click and reload included).
The application must be a class under the "objects" directory
and have to extend "p4a" class.
Attention, in p4a all object have to be assigned using the "=&"
operator or you will loose all references. You also have to
use the "&" operator in the method definition, when the method
returns objects (also for the class constructor).
Take a look at "sample_en" class for a better understanding.
*/

$sample_en =& p4a::singleton("Sample_En");
$sample_en->main();

?>