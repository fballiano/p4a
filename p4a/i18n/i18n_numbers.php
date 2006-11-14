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
	 * p4a internationalization class for numbers.
	 *
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_I18N_Numbers
	{
		/**
		 * Here we store all formats.
		 * @access private
		 * @var array
		 */
		var $formats = null;

		/**
		 * The decimal separator.
		 * @access private
		 * @var string
		 */
		var $decimal_separator = null;

		/**
		 * The thousand separator.
		 * @access private
		 * @var string
		 */
		var $thousand_separator = null;

		/**
		 * Class constructor.
		 * @param array				All formats in array.
		 * @access private
		 */
		function P4A_I18N_Numbers(&$formats)
		{
			$this->formats =& $formats;

			$float_format = $this->getFormat('float');
			$this->decimal_separator = $float_format[1];
			$this->thousand_separator = $float_format[2];
		}

		/**
		 * Format a number.
		 * Default format is float.
		 * @access public
		 * @param mixed		the number
		 * @param array		the format
		 * @return mixed
		 * @see P4A_Number::format()
		 */
		function format($number = 0, $format = null)
		{
			if ($format === null) {
				$format = $this->getFormat('float');
			}

			return P4A_Number::format($number, $format);
		}

		/**
		 * Format a number as an integer.
		 * @access public
		 * @param mixed		the number
		 * @return mixed
		 * @see format()
		 */
		function formatInteger($number = 0)
		{
			return $this->format($number, $this->getFormat('integer'));
		}

		/**
		 * Format a number as a float.
		 * @access public
		 * @param mixed		the number
		 * @return mixed
		 * @see format()
		 */
		function formatFloat($number = 0)
		{
			return $this->format($number, $this->getFormat('float'));
		}

		/**
		 * Format a number as a decimal.
		 * @access public
		 * @param mixed		the number
		 * @return mixed
		 * @see format()
		 */
		function formatDecimal($number = 0)
		{
			return $this->format($number, $this->getFormat('decimal'));
		}

		/**
		 * Unformat a number.
		 * Default format is float.
		 * @access public
		 * @param mixed		the number
		 * @param string	the format
		 * @return mixed
		 * @see P4A_Number::unformat()
		 */
		function unformat($number = 0, $format = NULL)
		{
			if ($format === NULL) {
				$format = $this->getFormat('float');
			}

			return P4A_Number::unformat($number, $format);
		}

		/**
		 * Unformat a number as an integer.
		 * @access public
		 * @param mixed		the number
		 * @return mixed
		 * @see unformat()
		 */
		function unformatInteger($number = 0)
		{
			return $this->unformat($number, $this->getFormat('integer'));
		}

		/**
		 * Unformat a number as a float.
		 * @access public
		 * @param mixed		the number
		 * @return mixed
		 * @see unformat()
		 */
		function unformatFloat($number = 0)
		{
			return $this->unformat($number, $this->getFormat('float'));
		}

		/**
		 * Unformat a number as a decimal.
		 * @access public
		 * @param mixed		the number
		 * @return mixed
		 * @see unformat()
		 */
		function unformatDecimal($number = 0)
		{
			return $this->unformat($number, $this->getFormat('decimal'));
		}

		/**
		 * Returns the format array for a given format name.
		 * @access public
		 * @param string	The format name
		 * @return array
		 */
		function getFormat($format)
		{
			return $this->formats[$format];
		}

		/**
		 * Sets the format array for a given format name.
		 * @access public
		 * @param string	The format name
		 * @param array		The format array
		 * @return array
		 */
		function setFormat($format, $value)
		{
			$this->formats[$format] = $value;
		}

		/**
		 * Sets the format (only decimals number) for a given format name, according to the current locale.
		 * @access public
		 * @param string	The format name
		 * @param integer	The number of decimals
		 * @return array
		 */
		function setLocalFormat($format, $decimals)
		{
			$this->formats[$format] = array($decimals, $this->decimal_separator, $this->thousand_separator);
		}
	}