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

require_once "Zend/Date.php";
require_once "Zend/Translate.php";

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
	 */
	private $locale = null;

	/**
	 * Here we store the current language.
	 * @var string
	 */
	private $language = null;

	/**
	 * Here we store the current country.
	 * @var string
	 */
	private $region = null;

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
	
	protected $_locale_engine = null;
	protected $_translation_engine = null;

	/**
	 * Class constructor.
	 * @param string				The desidered locale.
	 * @access private
	 */
	function p4a_i18n($locale = P4A_LOCALE)
	{
		$this->setLocale($locale);
	}

	/**
	 * Sets the desidered locale (it_IT|en_UK|en_US).
	 * @param string
	 */
	public function setLocale($locale = P4A_LOCALE)
	{
		$this->language = strtolower(substr($locale, 0, 2));
		$this->region = strtoupper(substr($locale, 3, 2));
		$this->locale = "{$this->language}_{$this->region}";
		
		$this->_locale_engine = new Zend_Locale($this->locale);
		$this->_translation_engine = new Zend_Translate(Zend_Translate::AN_ARRAY, array(), $this->locale);
		//$this->_translation_engine->addTranslation(P4A_APPLICATION_LOCALES_DIR, $this->locale);
	}

	/**
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @return string
	 */
	public function getRegion()
	{
		return $this->region;
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	public function translate($string)
	{
		return $this->_translation_engine->translate($string, $this->locale);
	}

	/**
	 * Calls the p4a default formatter for value with the given type.
	 * If the type in not recognized, $value is returned as is.
	 * @param mixed
	 * @param string	(date|time|integer|float|decimal|currency)
	 * @return mixed
	 */
	public function format($value, $type)
	{
		switch($type) {
			case 'boolean':
				$value = ($value == 1) ? 'yes' : 'no';
				$yes_no = $this->_locale_engine->getQuestion();
				return $yes_no[$value];
			case 'date':
				$date = new Zend_Date($value);
				return $date->get(Zend_Date::DATES, $this->_locale_engine);
			case 'time':
				$value = $this->datetime->formatTimeDefault($value);
				break;
			case 'integer':
				return Zend_Locale_Format::toNumber($value, array('precision'=>0, 'locale'=>$this->_locale_engine));
			case 'float':
				return Zend_Locale_Format::toNumber($value, array('precision'=>3, 'locale'=>$this->_locale_engine));
			case 'decimal':
				return Zend_Locale_Format::toNumber($value, array('precision'=>2, 'locale'=>$this->_locale_engine));
			case 'currency':
				$value = $this->currency->formatLocal($value);
				break;
		}

		return $value;
	}

	/**
	 * Calls the default normalizer for value with the given type.
	 * If the type in not recognized, $value is returned as is.
	 * @param mixed		The value to be normalized
	 * @param string	The type (date|time|integer|float|decimal|currency)
	 * @return mixed
	 */
	public function normalize($value, $type)
	{
		switch($type) {
			case 'boolean':
				//$yes_no = $this->_locale_engine->getQuestion('en_US');
				//print_r($yes_no);
				$value = ($value == $this->messages->get('yes')) ? 1 : 0;
				break;
			case 'date':
				$date =  Zend_Locale_Format::getDate($value, array('locale'=>$this->_locale_engine));
				return "{$date['year']}-{$date['month']}-{$date['day']}";
			case 'time':
				$value = $this->datetime->unformatTimeDefault($value);
				break;
			case 'integer':
				return Zend_Locale_Format::getInteger($value, array('locale'=>$this->_locale_engine));
			case 'float':
				return Zend_Locale_Format::getFloat($value, array('precision'=>3, 'locale'=>$this->_locale_engine));
			case 'decimal':
				return Zend_Locale_Format::getFloat($value, array('precision'=>2, 'locale'=>$this->_locale_engine));
			case 'currency':
				$value = $this->currency->unformatLocal($value);
				break;
		}

		return $value;
	}
}