<?php

/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */

if (!defined('P4A_I18N_DATE_CALENDAR_LANGUAGE')) {
	define('P4A_I18N_DATE_CALENDAR_LANGUAGE', 'en');
}

$numbers_formats = array
(
	"integer" => array('0', P4A_I18N_DECIMAL_SEPARATOR, P4A_I18N_THOUSAND_SEPARATOR),
	"decimal" => array('2', P4A_I18N_DECIMAL_SEPARATOR, P4A_I18N_THOUSAND_SEPARATOR),
	"float"   => array('*', P4A_I18N_DECIMAL_SEPARATOR, P4A_I18N_THOUSAND_SEPARATOR)
);

$datetime_formats = array
(
	"date_default"	=>	'%Y-%m-%d',
	"date_medium"	=>	'%Y-%m-%d',
	"date_long"		=>	'%Y-%m-%d',
	"date_full"		=>	'%Y-%m-%d',

	"time_default"	=>	'%H:%M',
	"time_long"		=>	'%H:%M:%S'
);