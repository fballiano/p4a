<?php

// try different results changing P4A_LOCALE,
// remember that you've to restart your browser
// to see the changes

define('P4A_LOCALE', 'en_US');
//define('P4A_LOCALE', 'it_IT');
//define('P4A_LOCALE', 'es_ES');

require_once dirname(__FILE__) . '/../../p4a.php';

$app = p4a::singleton('sample_i18n');
$app->main();