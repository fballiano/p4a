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
	 * p4a internationalization class for date/time.
	 *
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_I18N_DATETIME
	{
		/**
		 * Here we store all formats.
		 * @access private
		 * @var array
		 */
		var $formats = NULL;

		/**
		 * Here we store all formats.
		 * @access private
		 * @var array
		 */
		var $locale_vars = NULL;

		/**
		 * Class constructor.
		 * @param array				All formats in array.
		 * @access private
		 */
		function &p4a_i18n_datetime(&$formats, &$locale_vars)
		{
			$this->formats =& $formats;
			$this->locale_vars =& $locale_vars;
		}

		/**
		 * Format a date using a format.
		 * @access public
		 * @param string		The date in YYYY-MM-DD HH:MM:SS
		 * @param array			The format (see set_format)
		 * @return string
		 */
		function format($date = NULL, $format = P4A_DATETIME)
		{
			return DATE::format($date, $format, $this->locale_vars);
		}

		/**
		 * Format a date using the default format.
		 * @access public
		 * @param string		The date in YYYY-MM-DD HH:MM:SS
		 * @return string
		 */
		function formatDateDefault($date = NULL)
		{
			return DATE::format($date, $this->getFormat('date_default'), $this->locale_vars);
		}

		/**
		 * Format a date using the "medium" format.
		 * @access public
		 * @param string		The date in YYYY-MM-DD HH:MM:SS
		 * @return string
		 */
		function formatDateMedium($date = NULL)
		{
			return DATE::format($date, $this->getFormat('date_medium'), $this->locale_vars);
		}

		/**
		 * Format a date using the "long" format.
		 * @access public
		 * @param string		The date in YYYY-MM-DD HH:MM:SS
		 * @return string
		 */
		function formatDateLong($date = NULL)
		{
			return DATE::format($date, $this->getFormat('date_long'), $this->locale_vars);
		}

		/**
		 * Format a date using the "full" format.
		 * @access public
		 * @param string		The date in YYYY-MM-DD HH:MM:SS
		 * @return string
		 */
		function formatDateFull($date = NULL)
		{
			return DATE::format($date, $this->getFormat('date_full'), $this->locale_vars);
		}

		/**
		 * Unformat a date formatted with a format.
		 * After unformatting, returns the date formatting it with $output_format.
		 * @access public
		 * @param string		The date
		 * @param array			The input format(see set_format)
		 * @param array			The output format(see set_format)
		 * @return string
		 * @see set_format()
		 */
		function unformat($date, $format, $output_format = P4A_DATETIME)
		{
			return DATE::unformat($date, $format, $output_format);
		}

		/**
		 * Unformat a date formatted with a format.
		 * Returns a date formatted with the P4A_DATE date format.
		 * @access public
		 * @param string		The date
		 * @param array			The input format(see set_format)
		 * @return string
		 * @see set_format()
		 */
		function unformatDate($date, $format)
		{
			return DATE::unformat($date, $format, P4A_DATE);
		}

		/**
		 * Unformat a date formatted with a format.
		 * Assumes that data is formatted with the default date format.
		 * Returns a date formatted with the P4A_DATE date format.
		 * @access public
		 * @param string		The date
		 * @return string
		 */
		function unformatDateDefault($date)
		{
			return DATE::unformat($date, $this->getFormat('date_default'), P4A_DATE);
		}

		/**
		 * Unformat a date formatted with a format.
		 * Assumes that data is formatted with the "medium" date format.
		 * Returns a date formatted with the P4A_DATE date format.
		 * @access public
		 * @param string		The date
		 * @return string
		 */
		function unformatDateMedium($date)
		{
			return DATE::unformat($date, $this->getFormat('date_medium'), P4A_DATE);
		}

		/**
		 * Unformat a date formatted with a format.
		 * Assumes that data is formatted with the "long" date format.
		 * Returns a date formatted with the P4A_DATE date format.
		 * @access public
		 * @param string		The date
		 * @return string
		 */
		function unformatDateLong($date)
		{
			return DATE::unformat($date, $this->getFormat('date_long'), P4A_DATE);
		}

		/**
		 * Unformat a date formatted with a format.
		 * Assumes that data is formatted with the "full" date format.
		 * Returns a date formatted with the P4A_DATE date format.
		 * @access public
		 * @param string		The date
		 * @return string
		 */
		function unformatDateFull($date)
		{
			return DATE::unformat($date, $this->getFormat('date_full'), P4A_DATE);
		}

		/**
		 * Format a time with in the default time format.
		 * @access public
		 * @param string		The time in HH:MM:SS format
		 * @return string
		 */
		function formatTimeDefault($time = NULL)
		{
			if ($time !== NULL) {
				$time = "0000-01-01 $time";
			}

			return DATE::format($time, $this->getFormat('time_default'));
		}

		/**
		 * Format a time in the "short" time format.
		 * @access public
		 * @param string		The time in HH:MM:SS format
		 * @return string
		 */
		function formatTimeShort($time = NULL)
		{
			if ($time !== NULL) {
				$time = "0000-01-01 $time";
			}

			return DATE::format('0000-01-01 ' . $time, $this->getFormat('time_short'));
		}

		/**
		 * Unformat a formatted time (in default format) and returns it formatted in P4A_TIME format.
		 * @access public
		 * @param string		The time
		 * @return string
		 */
		function unformatTimeDefault($time = NULL)
		{
			return DATE::unformat($time, $this->getFormat('time_default'), P4A_TIME);
		}

		/**
		 * Unformat a formatted time (in "short" format) and returns it formatted in P4A_TIME format.
		 * @access public
		 * @param string		The time
		 * @return string
		 */
		function unformatTimeShort($time = NULL)
		{
			return DATE::unformat($time, $this->getFormat('time_short'), P4A_TIME);
		}

		/**
		 * Returns a format identified by a name.
		 * @access public
		 * @param string		The format name.
		 * @return array
		 */
		function getFormat($format)
		{
			return $this->formats[$format];
		}

		/**
		 * Sets a format.
		 * Format is like this: array('% Eur', '2', ',', '.').
		 * @access public
		 * @param string		The format name
		 * @param array			The format
		 */
		function setFormat($format, $value = P4A_DATETIME)
		{
			$this->formats[$format] = $value;
		}
	}

?>