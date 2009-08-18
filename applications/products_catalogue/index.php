<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with P4A.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * To contact the authors write to:                                     <br />
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

// Select application's locale
define("P4A_LOCALE", 'en_US');

// Connect to the database (if you want you can add "?charset=YOURCHARSET" to the DSN)
define("P4A_DSN", 'mysql://root:@localhost/p4a_products_catalogue');
// define("P4A_DSN", 'pgsql://p4a:p4a@localhost/p4a_products_catalogue');
// define("P4A_DSN", 'oci://p4a:p4a@localhost/xe');
// define("P4A_DSN", 'sqlite:/p4a_products_catalogue');

// Enable more error details
// define("P4A_EXTENDED_ERRORS", true);

// Disable AJAX during the development phase, it will allows you
// a faster debug, enable it on the production server
// define("P4A_AJAX_ENABLED", false);

// Path (on server) where P4A will write all code transferred via AJAX
// define("P4A_AJAX_DEBUG", "/tmp/p4a_ajax_debug.txt");

require_once dirname(__FILE__) . '/../../p4a.php';

// Check Installation and configuration.
// This lines should be removed after the first run.
$check = p4a_check_configuration();

// Here we go
if (is_string($check)) {
	print $check;
} else {
	$p4a = p4a::singleton("products_catalogue");
	$p4a->main();
}