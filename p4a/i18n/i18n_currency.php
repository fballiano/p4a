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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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
	 * p4a internationalization class for currency.
	 *
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_I18N_Currency
	{
		/**
		 * Here we store all formats.
		 * @access private
		 * @var array
		 */
		var $formats = NULL;

		/**
		 * The decimal separator.
		 * @access private
		 * @var string
		 */
		var $decimal_separator = NULL;

		/**
		 * The thousand separator.
		 * @access private
		 * @var string
		 */
		var $thousand_separator = NULL;

		/**
		 * Class constructor.
		 * @param array				All formats in array.
		 * @access private
		 */
		function &P4A_I18N_Currency( &$formats )
		{
			$this->formats =& $formats;

			$local_format = $this->getFormat('local');
			$this->decimal_separator = $local_format[2];
			$this->thousand_separator = $local_format[3];
		}

		/**
		 * Returns the format array for a given format name.
		 * @access public
		 * @param string	The format name
		 * @return array
		 */
		function getFormat( $format )
		{
			return $this->formats[ $format ];
		}

		/**
		 * Sets the format array for a given format name.
		 * @access public
		 * @param string	The format name
		 * @param array		The format array
		 * @return array
		 */
		function setFormat( $format, $value )
		{
			$this->formats[ $format ] = $value;
		}

		/**
		 * Sets the format (only decimals number) for a given format name, according to the current locale.
		 * @access public
		 * @param string	The format name
		 * @param integer	The formatting string. Eg: "% Eur"
		 * @param integer	The number of decimals
		 * @return array
		 */
		function setLocalFormat( $format, $format_string, $decimals )
		{
			$this->formats[ $format ] = array( $format_string, $decimals, $this->decimal_separator, $this->thousand_separator );
		}

		/**
		 * Format a currency.
		 * Default format is local.
		 * @access public
		 * @param mixed		the value
		 * @param array		the format
		 * @return mixed
		 * @see NUMBER::format()
		 */
		function format( $value = 0, $format = NULL )
		{
			if( $format === NULL ) {
				$format = $this->getFormat('local');
			}

			$value = NUMBER::format($value, array_slice($format, 1));
			return str_replace('%', $value, $format[0]);
		}

		/**
		 * Format a value in local format.
		 * @access public
		 * @param mixed		the value
		 * @return mixed
		 * @see format()
		 */
		function formatLocal( $value = 0 )
		{
			return $this->format($value, $this->getFormat('local'));
		}

		/**
		 * Format a value in international format.
		 * @access public
		 * @param mixed		the value
		 * @return mixed
		 * @see format()
		 */
		function formatInternational( $value = 0 )
		{
			return $this->format($value, $this->getFormat('international'));
		}

		/**
		 * Unformat a currency.
		 * Default format is local.
		 * @access public
		 * @param mixed		the value
		 * @param array		the format
		 * @return mixed
		 * @see NUMBER::format()
		 */
		function unformat( $value = 0, $format = NULL )
		{
			if( $format === NULL ) {
				$format = $this->getFormat('local');
			}

			return NUMBER::unformat($value, array_slice($format, 1));
		}

		/**
		 * Unformat a value in local format.
		 * @access public
		 * @param mixed		the value
		 * @return mixed
		 * @see format()
		 */
		function unformatLocal( $value = 0 )
		{
			return $this->unformat($value, $this->getFormat('local'));
		}

		/**
		 * Unformat a value in international format.
		 * @access public
		 * @param mixed		the value
		 * @return mixed
		 * @see format()
		 */
		function unformatInternational( $value = 0 )
		{
			return $this->unformat($value, $this->getFormat('international'));
		}
	}

?>