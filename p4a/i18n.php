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
	 * p4a internationalization class.
	 *
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_I18N
	{
		/**
		 * Here we store the current locale.
		 * @var string
		 * @access private
		 */
		var $locale = NULL;

		/**
		 * Here we store the current language.
		 * @var string
		 * @access private
		 */
		var $language = NULL;

		/**
		 * Here we store the current country.
		 * @var string
		 * @access private
		 */
		var $country = NULL;

		/**
		 * Here we store the current codepage.
		 * @var string
		 * @access private
		 */
		var $codepage = NULL;

		/**
		 * Here we store the current charset. Default is UTF-8.
		 * @var string
		 * @access private
		 */
		var $charset = 'UTF-8';

		/**
		 * Currency management object.
		 * @var I18N_CURRENCY
		 * @access public
		 */
		var $currency = NULL;

		/**
		 * Here we store all formats for currency data.
		 * @var array
		 * @access private
		 */
		var $currency_formats = NULL;

		/**
		 * Date/Time management object.
		 * @var I18N_DATETIME
		 * @access public
		 */
		var $datetime = NULL;

		/**
		 * Here we store all formats for date/time data.
		 * @var array
		 * @access private
		 */
		var $datetime_formats = NULL;

		/**
		 * Messages management object.
		 * @var I18N_MESSAGES
		 * @access public
		 */
		var $messages = NULL;

		/**
		 * Numbers management object.
		 * @var I18N_NUMBERS
		 * @access public
		 */
		var $numbers = NULL;

		/**
		 * Here we store all formats for numeric data.
		 * @var array
		 * @access private
		 */
		var $numbers_formats = NULL;

		/**
		 * Class constructor.
		 * @param string				The desidered locale.
		 * @access private
		 */
		function &p4a_i18n($locale = P4A_LOCALE)
		{
			$this->setLocale($locale);
		}

		/**
		 * Sets the desidered locale (it_IT|en_UK|en_US). Locale name can contain codepage (ru_RU.KOI-8|ru_RU.CP1251).
		 * @param string				The desired locale.
		 * @access public
		 */
		function setLocale($locale = P4A_LOCALE)
		{
			$this->language = strtolower(substr($locale, 0, 2));
			$this->country = strtoupper(substr($locale, 3, 2));
			$this->codepage = strtoupper(substr($locale, 6));
			$this->locale = "{$this->language}_{$this->country}";

			if ($this->codepage) {
				$this->locale .= ".{$this->codepage}";
			}

			$this->setSystemLocale();
			$this->loadFormats();

			unset( $this->messages ) ;
			$this->messages =& new p4a_i18n_messages($this->language, $this->country, $this->codepage);

			unset( $this->numbers ) ;
			$this->numbers =& new p4a_i18n_numbers($this->numbers_formats);

			unset( $this->currency ) ;
			$this->currency =& new p4a_i18n_currency($this->currency_formats);

			unset( $this->datetime ) ;
			$this->datetime =& new p4a_i18n_datetime($this->datetime_formats, $this->messages->messages);
		}

		/**
		 * Sets PHP system level locale
		 * @access private
		 */
		function setSystemLocale()
		{
			setlocale(LC_ALL, $this->locale);
		}

		/**
		 * Returns the current locale.
		 * @return string
		 * @access public
		 */
		function getLocale()
		{
			return $this->locale;
		}

		/**
		 * Returns the current language.
		 * @return string
		 * @access public
		 */
		function getLanguage()
		{
			return $this->language;
		}

		/**
		 * Returns the current country.
		 * @return string
		 * @access public
		 */
		function getCountry()
		{
			return $this->country;
		}

		/**
		 * Sets the charset.
		 * @access public
		 * @param string		The charset
		 */
		function setCharset($charset = 'UTF-8')
		{
			$this->charset = $charset;
		}

		/**
		 * Returns the current charset.
		 * @return string
		 * @access public
		 */
		function getCharset()
		{
			return $this->charset;
		}

		/**
		 * Loads all available formats.
		 * @access private
		 */
		function loadFormats()
		{
			$cp = ($this->codepage ? ".$this->codepage" : "");
			include(dirname(__FILE__) . "/i18n/formats/{$this->language}/{$this->country}{$cp}.php");

			if (isset($text_formats['charset'])) {
				$this->charset = $text_formats['charset'];
			}
			unset($text_formats);

			$this->numbers_formats = $numbers_formats;
			unset($numbers_formats);
			$this->datetime_formats = $datetime_formats;
			unset($datetime_formats);
			$this->currency_formats = $currency_formats;
			unset($currency_formats);
		}

		/**
		 * Calls the p4a default formatter for value with the given type.
		 * If the type in not recognized, $value is returned as is.
		 * @access public
		 * @param mixed		The value to be formatter
		 * @param string	The type (date|time|integer|float|decimal|currency)
		 * @return mixed
		 */
		function autoFormat($value, $type)
		{
			switch( $type )
			{
				case 'date':
					$value = $this->datetime->formatDateDefault($value);
					break;
				case 'time':
					$value = $this->datetime->formatTimeDefault($value);
					break;
				case 'integer':
					$value = $this->numbers->formatInteger($value);
					break;
				case 'float':
					$value = $this->numbers->formatFloat($value);
					break;
				case 'decimal':
					$value = $this->numbers->formatDecimal($value);
					break;
				case 'currency':
					$value = $this->currency->formatLocal($value);
					break;
			}

			return $value;
		}

		/**
		 * Calls the p4a default unformatter for value with the given type.
		 * If the type in not recognized, $value is returned as is.
		 * @access public
		 * @param mixed		The value to be unformatter
		 * @param string	The type (date|time|integer|float|decimal|currency)
		 * @return mixed
		 */
		function autoUnformat($value, $type)
		{
			switch( $type )
			{
				case 'date':
					$value = $this->datetime->unformatDateDefault($value);
					break;
				case 'time':
					$value = $this->datetime->unformatTimeDefault($value);
					break;
				case 'integer':
					$value = $this->numbers->unformatInteger($value);
					break;
				case 'float':
					$value = $this->numbers->unformatFloat($value);
					break;
				case 'decimal':
					$value = $this->numbers->unformatDecimal($value);
					break;
				case 'currency':
					$value = $this->currency->unformatLocal($value);
					break;
			}

			return $value;
		}
	}

?>