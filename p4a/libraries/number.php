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

	/**
	 * Number Class.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Number
	{
		/**
		 * Format a number using a format.
		 * Format is PHP number_format compatible.
		 * @access public
		 * @param string		The number
		 * @param array			The format
		 * @return string
		 */
		function format($number = null, $format)
		{
			if ($number === null) {
				return '';
			}

			if ($format[0] == '*') {
				$parts = explode('.', $number);

				if (array_key_exists(1, $parts)) {
					$format[0] = strlen($parts[1]);
				} else {
					$format[0] = 0;
				}
			}

			return call_user_func_array('number_format', array_merge(array($number), $format));
		}

		/**
		 * UnFormat a number formatted using "format".
		 * This function takes a formatted number back to its international notation.
		 * @access public
		 * @param string		The number
		 * @param array			The format used in formatting phase
		 * @return string
		 * @see format()
		 */
		function unformat($number = 0, $format)
		{
			$decimal_separator = $format[1];

			$number = preg_replace("/[^0-9\\" . $decimal_separator . "]/", '', $number);
			$number = str_replace($decimal_separator, '.', $number);

			$number = P4A_Number::format($number, array( $format[0], '.', ''));
			return $number;
		}
	}

?>