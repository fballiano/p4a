<?php

// generated with locale data 1.3

header('Content-type: text/plain; charset=UTF-8'); 
error_reporting(E_ALL ^ E_NOTICE);
ini_set('include_path', '.;../../p4a/libraries/pear');
set_time_limit(0);
require 'XML/Unserializer.php';
require 'System.php';

System::rm('-r output');
System::mkDir('-p output');

$locales = array();
$options = array('complexType'=>'array', 'returnResult'=>true, 'keyAttribute'=>'type');
$unserializer = new XML_Unserializer($options);

// reading files
$dh = opendir('data');
while (false !== ($file = readdir($dh))) {
	if (substr_count($file, '_') == 1 and substr_count($file, '#') == 0) {
		$locales[] = str_replace('.xml', '', $file);
	}
}

foreach ($locales as $locale) {
	print "\n-- $locale --\n";
	list($language, $nation) = explode('_', $locale);
	
	$end = $unserializer->unserialize(file_get_contents("data/$locale.xml"));
	$mid = $unserializer->unserialize(file_get_contents("data/$language.xml"));
	$root = $unserializer->unserialize(file_get_contents("data/root.xml"));
	
	$date_short = extract_date_format('short', $end, $mid, $root);
	$date_medium = extract_date_format('medium', $end, $mid, $root);
	$date_long = extract_date_format('long', $end, $mid, $root);
	$date_full = extract_date_format('full', $end, $mid, $root);
	$time_medium = extract_time_format('medium', $end, $mid, $root);
	
	if (check($date_short) and check($date_medium) and check($date_long) and check($date_full)) {
		System::mkDir("-p output/$language");
		
		$towrite = file_get_contents('template.php');
		
		//$towrite = str_replace('[DS]', addslashes($i['mon_decimal_point']), $towrite);
		//$towrite = str_replace('[TS]', addslashes($i['mon_thousands_sep']), $towrite);
		
		$towrite = str_replace('[DATE_DEFAULT]', $date_short, $towrite);
		$towrite = str_replace('[DATE_MEDIUM]', $date_medium, $towrite);
		$towrite = str_replace('[DATE_LONG]', $date_long, $towrite);
		$towrite = str_replace('[DATE_FULL]', $date_full, $towrite);
		
		//$towrite = str_replace('[TIME_DEFAULT]', $dates[4], $towrite);
		//$towrite = str_replace('[TIME_LONG]', $dates[5], $towrite);
		$fp = fopen("output/$language/$nation.php", 'w');
		fwrite($fp, $towrite);
		fclose($fp);
	} else {
		print "\n---------------------------------------------------------NON VALIDO\n";
	}
	
	flush();
}

function extract_date_format($type, $end, $mid, $root)
{
	if (isset($end['dates']['calendars']['gregorian']['dateFormats'][$type]['dateFormat']['pattern'])) {
		$date = $end['dates']['calendars']['gregorian']['dateFormats'][$type]['dateFormat']['pattern'];
	} elseif (isset($mid['dates']['calendars']['gregorian']['dateFormats'][$type]['dateFormat']['pattern'])) {
		$date = $mid['dates']['calendars']['gregorian']['dateFormats'][$type]['dateFormat']['pattern'];
	} else {
		$date = $root['dates']['calendars']['gregorian'][1]['dateFormats'][$type]['dateFormat']['pattern'];
	}
	
	if (is_array($date)) {
		$date = $date[0];
	}
	
	return parse_date($date);
}

function parse_date($date)
{
	$splitter = "/('.*?')/";
	$date = trim($date);
	print "$date\n";
	
	$date = str_replace("''", '__SINGLE_QUOTE__', $date);
	$split = preg_split($splitter, $date, -1, PREG_SPLIT_DELIM_CAPTURE);
	$return = '';
	
	$p = array("/'(.*?)'/", '/yyyy/i', '/yy/i', '/mmmm/i', '/mmm/i', '/mm/i', '/(^|[^%])m/i', '/eeee/i', '/eee/i', '/dd/i', '/(^|[^%])d/i','/g/i');
	$r = array('$1', '%Y', '%Y', '%B', '%b', '%m', '$1%m', '%A', '%a', '%d', '$1%e', '%Z');
	
	foreach ($split as $part) {
		if (preg_match($splitter, $part)) {
			$part = substr($part, 1, -1);
		} else {
			$part = preg_replace($p, $r, $part);
		}
		$return .= $part;
	}
	
	if (preg_match("/(^|[^%])\w/", $return)) {
		print '---------------------------------------------------------';
	}
	$return = str_replace('__SINGLE_QUOTE__', "\'", $return);
	print "$return\n\n";
	return $return;
}

function extract_time_format($type, $end, $mid, $root)
{
	if (isset($end['dates']['calendars']['gregorian']['timeFormats'][$type]['timeFormat']['pattern'])) {
		$time = $end['dates']['calendars']['gregorian']['timeFormats'][$type]['timeFormat']['pattern'];
	} elseif (isset($mid['dates']['calendars']['gregorian']['timeFormats'][$type]['timeFormat']['pattern'])) {
		$time = $mid['dates']['calendars']['gregorian']['timeFormats'][$type]['timeFormat']['pattern'];
	} else {
		$time = $root['dates']['calendars']['gregorian'][1]['timeFormats'][$type][1]['timeFormat']['pattern'];
	}
	
	if (is_array($time)) {
		$time = $time[0];
	}
	
	return parse_time($time);
}

function parse_time($time)
{
	print "$time\n";
	$p = array("/HH/", "/h/", "/mm/i", "/ss/i", "/a/i");
	$r = array('%H', '%I', '%M', '%S', '%p');
	$return = preg_replace($p, $r, $time);
	
	if (preg_match("/[^%][a-zA-Z]/", $return)) die("ERRORE!!! $time $return");
	
	print "$return\n";
	return $return;
}

function check($string) {
	if (empty($string)) return false;
	if (substr_count($string, '?') > 0) return false;
	return true;
}

/*******************************************************************/
die();
system('rm -r output');
$locales = file('locales');
$clean_locales = array();

// cleaning duplicated locales, first pass
foreach ($locales as $locale) {
	list($locale, $encoding) = explode(' ', trim($locale));
	if (strpos($locale, '_') === false) {
		continue;
	}
	
	$locale = explode('.', $locale);
	$locale = $locale[0];

	$locale = explode('@', $locale);
	$locale = $locale[0];	
	
	$clean_locales[trim($locale)] = $encoding;
}

// cleaning duplicated locales, second pass
foreach ($clean_locales as $locale=>$encoding) {
	$encoding = str_replace('-', '', strtolower($encoding));
	if (!I18Nv2::setLocale("$locale.$encoding")) {
		unset($clean_locales[$locale]);
	}
}

// writing it all
$template = file_get_contents('template.php');
foreach ($clean_locales as $locale=>$encoding) {
	$encoding = str_replace('-', '', strtolower($encoding));
	list($dir, $file) = explode('_', $locale);
	system::mkdir("-p output/{$dir}");
	
	print "$locale\n";
	I18Nv2::setLocale("$locale.$encoding");
	$i = I18Nv2::getInfo();
	$l = I18Nv2::createLocale($locale);
	$dates = array();
	$date_available = exec("./date_format_extractor.pl $locale", $dates);
	convert_perl_formats($dates[0]);
	convert_perl_formats($dates[1]);
	convert_perl_formats($dates[2]);
	convert_perl_formats($dates[3]);
	convert_perl_formats($dates[4]);
	convert_perl_formats($dates[5]);

	$towrite = file_get_contents('template.php');
	$towrite = str_replace('[DS]', addslashes($i['mon_decimal_point']), $towrite);
	$towrite = str_replace('[TS]', addslashes($i['mon_thousands_sep']), $towrite);
	
	if (!$date_available) {
		$towrite = mb_ereg_replace('\$datetime_formats(.*?);', '// we need date and time formats', $towrite, 'm');
	} else {
		$towrite = str_replace('[DATE_DEFAULT]', $dates[0], $towrite);
		$towrite = str_replace('[DATE_MEDIUM]', $dates[1], $towrite);
		$towrite = str_replace('[DATE_LONG]', $dates[2], $towrite);
		$towrite = str_replace('[DATE_FULL]', $dates[3], $towrite);
		
		$towrite = str_replace('[TIME_DEFAULT]', $dates[4], $towrite);
		$towrite = str_replace('[TIME_LONG]', $dates[5], $towrite);
	}
	
	$towrite = str_replace('[LOCAL_CURRENCY_DECIMAILS]', $l->currencyFormats['local'][1], $towrite);
	$towrite = str_replace('[INTERNATIONAL_CURRENCY_DECIMAILS]', $l->currencyFormats['international'][1], $towrite);
	
	if ($i['p_sep_by_space']) {
		$i['currency_symbol'] = ' ' . $i['currency_symbol'] . ' ';
		$i['int_curr_symbol'] = ' ' . $i['int_curr_symbol'] . ' ';
	}
	
	$local_currency_print = '%';
	if ($i['p_cs_precedes']) {
		$local_currency_print = $i['currency_symbol'] . $local_currency_print;
	} else {
		$local_currency_print .= $i['currency_symbol'];
	}
	$towrite = str_replace('[LOCAL_CURRENCY_PRINT]', trim($local_currency_print), $towrite);
	
	$international_currency_print = '%';
	if ($i['p_cs_precedes']) {
		$international_currency_print = $i['int_curr_symbol'] . $international_currency_print;
	} else {
		$international_currency_print .= $i['int_curr_symbol'];
	}
	$towrite = str_replace('[INTERNATIONAL_CURRENCY_PRINT]', trim($international_currency_print), $towrite);
	
	$fp = fopen("output/{$dir}/{$file}.php", 'w');
	fwrite($fp, iconv($encoding, 'UTF-8', $towrite));
	fclose($fp);
}

// converting some perl formats errors
function convert_perl_formats(&$input)
{
	$input = str_replace('%{ce_year}', '%Y', $input);
	$input = str_replace('%{month}', '%m', $input);
	$input = str_replace('%{day}', '%d', $input);
	$input = str_replace('%{hour_12}', '%l', $input);
	$input = str_replace('%{hour}', '%H', $input);
	$input = str_replace('%{era}', '', $input);
	$input = str_replace('%y', '%Y', $input);

	$input = trim($input);

	if (strpos($input, '{') !== false) {
		die($input);
	}
}

?>
