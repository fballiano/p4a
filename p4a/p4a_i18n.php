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
 * Fabrizio Balliano <fabrizio@fabrizioballiano.it>                     <br />
 * Andrea Giardina <andrea.giardina@crealabs.it>
 *
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

require_once "Zend/Date.php";
require_once "Zend/Registry.php";
require_once "Zend/Translate.php";
require_once "Zend/Translate/Adapter/Array.php";

/**
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class P4A_I18N
{
	/**
	 * @var string
	 */
	private $locale = null;

	/**
	 * @var string
	 */
	private $language = null;

	/**
	 * @var string
	 */
	private $region = null;
	
	/**
	 * @var integer
	 */
	private $first_day_of_the_week = 0;
	
	/**
	 * @var Zend_Locale
	 */
	protected $_locale_engine = null;
	
	/**
	 * @var Zend_Translate_Adapter_Array
	 */
	protected $_translation_engine = null;

	/**
	 * @param string $locale
	 */
	public function __construct($locale = P4A_LOCALE)
	{
		$this->setLocale($locale);
	}

	/**
	 * Sets the desidered locale (it_IT|en_UK|en_US).
	 * @param string
	 */
	public function setLocale($locale = P4A_LOCALE)
	{
		// removing eventual ".charset" part
		$locale = explode(".", $locale);
		$locale = $locale[0];
		
		list($this->language, $this->region) = explode("_", $locale);
		$this->language = strtolower($this->language);
		$this->region = strtoupper($this->region);
		$this->locale = "{$this->language}_{$this->region}";
		
		$this->_locale_engine = new Zend_Locale($this->locale);
		$this->setFirstDayOfTheWeek();
		
		$messages = array();
		$this->mergeTranslationFile(dirname(__FILE__) . "/i18n/{$this->language}/LC_MESSAGES/p4a.mo", $messages);
		$this->mergeTranslationFile(dirname(__FILE__) . "/i18n/{$this->locale}/LC_MESSAGES/p4a.mo", $messages);
		
		$this->_translation_engine = new Zend_Translate(Zend_Translate::AN_ARRAY, $messages, $this->locale, array("disableNotices" => true));
		Zend_Registry::set('Zend_Translate', $this->_translation_engine);
	}
	
	/**
	 * Reads a translation file (gettext) and merges it to the messages array
	 *
	 * @param string $file
	 * @param array $messages
	 */
	private function mergeTranslationFile($file, &$messages)
	{
		if (file_exists($file)) {
			$translate = new Zend_Translate('gettext', $file, $this->locale);
			$new_messages = $translate->getMessages();
			if (is_array($new_messages)) {
				$messages = array_merge($messages, $new_messages);
			}
		}
	}
	
	/**
	 * Adds translation messages to the translation pool
	 * @param array $messages
	 * @return P4A_I18N
	 */
	public function mergeTranslation(array $messages)
	{
		$this->_translation_engine->addTranslation($messages, $this->locale);
		return $this;
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
		if (strlen($string) == 0) return '';
		$translation = $this->_translation_engine->translate($string, $this->locale);
		$is_translated = $this->_translation_engine->isTranslated($string, $this->locale);
		p4a::singleton()->actionHandler('ontranslate', $string, $translation, $is_translated);
		return $translation;
	}

	/**
	 * Reads a normalized value, localizes and returns it.
	 * Returns an empty string if no value is passed.
	 * @param mixed $value
	 * @param string $type (boolean|date|time|integer|float|decimal|filesize)
	 * @param integer $num_of_decimals used only if type is float or decimal
	 * @param boolean $throw_exception do you want this function to throw an exception on error?
	 * @return mixed
	 */
	public function format($value, $type, $num_of_decimals = null, $throw_exception = true)
	{
		if ($throw_exception) {
			return $this->_format($value, $type, $num_of_decimals);
		}
		
		try {
			return $this->_format($value, $type, $num_of_decimals);
		} catch (Exception $e) {
			return $value;
		}
	}
	
	/**
	 * Reads a normalized value, localizes and returns it.
	 * Returns an empty string if no value is passed.
	 * @param mixed $value
	 * @param string $type (boolean|date|time|integer|float|decimal|filesize)
	 * @param integer $num_of_decimals used only if type is float or decimal
	 * @return mixed
	 */
	private function _format($value, $type, $num_of_decimals)
	{
		if (strlen($value) == 0) return '';
		
		switch($type) {
			case 'boolean':
				$value = ($value == 1) ? 'yes' : 'no';
				$yes_no = Zend_Locale::getQuestion($this->_locale_engine);
				return $yes_no[$value];
			case 'date':
				$date = new Zend_Date($value, "yyyy-MM-dd", $this->_locale_engine);
				return $date->get(Zend_Date::DATES, $this->_locale_engine);
			case 'time':
				$date = new Zend_Date($value, "HH:mm:ss", $this->_locale_engine);
				return $date->get(Zend_Date::TIME_SHORT, $this->_locale_engine);
			case 'datetime':
				$date = new Zend_Date($value, "yyyy-MM-dd HH:mm:ss", $this->_locale_engine);
				return $date->get(Zend_Date::DATES, $this->_locale_engine) . ' ' . $date->get(Zend_Date::TIME_MEDIUM, $this->_locale_engine);
			case 'integer':
				return Zend_Locale_Format::toNumber($value, array('precision'=>0, 'locale'=>$this->_locale_engine));
			case 'float':
				if ($num_of_decimals === null) $num_of_decimals = 3;
				return Zend_Locale_Format::toNumber($value, array('precision'=>$num_of_decimals, 'locale'=>$this->_locale_engine));
			case 'decimal':
				if ($num_of_decimals === null) $num_of_decimals = 2;
				return Zend_Locale_Format::toNumber($value, array('precision'=>$num_of_decimals, 'locale'=>$this->_locale_engine));
			case 'filesize':
				if ($num_of_decimals === null) $num_of_decimals = 2;
				$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
				for ($i=0; $value > 1024 and $i < count($units) - 1; $i++) $value /= 1024;
				return Zend_Locale_Format::toNumber($value, array('precision'=>$num_of_decimals, 'locale'=>$this->_locale_engine)) . " {$units[$i]}";
		}

		return $value;
	}
	
	/**
	 * Reads a localized value, normalizes and returns it.
	 * Returns an empty string if no value is passed.
	 * @param mixed $value
	 * @param string $type (boolean|date|time|integer|float|decimal|currency)
	 * @param integer $num_of_decimals used only if type is float or decimal
	 * @param boolean $throw_exception do you want this function to throw an exception on error?
	 * @return mixed
	 */

	/**
	 * Reads a localized value, normalizes and returns it
	 * @param mixed $value
	 * @param string $type (boolean|date|time|integer|float|decimal|currency)
	 * @param integer $num_of_decimals used only if type is float or decimal
	 * @param boolean $throw_exception do you want this function to throw an exception on error?
	 * @return mixed
	 */
	public function normalize($value, $type, $num_of_decimals = 0, $throw_exception = true)
	{
		if ($throw_exception) {
			return $this->_normalize($value, $type, $num_of_decimals);
		}
		
		try {
			return $this->_normalize($value, $type, $num_of_decimals);
		} catch (Exception $e) {
			return $value;
		}
	}
	
	/**
	 * Reads a localized value, normalizes and returns it.
	 * Returns an empty string if no value is passed.
	 * @param mixed $value
	 * @param string $type (boolean|date|time|integer|float|decimal|currency)
	 * @return mixed
	 */
	private function _normalize($value, $type, $num_of_decimals)
	{
		if (strlen($value) == 0) return '';
		
		switch($type) {
			case 'boolean':
				$yes_no = Zend_Locale_Data::getContent($this->_locale_engine, 'questionstrings');
				$yes_regexp = '/^(' . str_replace(':', '|', $yes_no['yes']) . ')$/i';
				if (preg_match($yes_regexp, $value)) return 1;
				return 0;
			case 'date':
				$date =  Zend_Locale_Format::getDate($value, array('locale'=>$this->_locale_engine, 'fix_date'=>true));
				$date['month'] = str_pad($date['month'], 2, 0, STR_PAD_LEFT);
				$date['day'] = str_pad($date['day'], 2, 0, STR_PAD_LEFT);
				return "{$date['year']}-{$date['month']}-{$date['day']}";
			case 'time':
				$date_format = Zend_Locale_Format::getTimeFormat($this->_locale_engine);
				$date =  Zend_Locale_Format::getDate($value, array('date_format'=>$date_format, 'locale'=>$this->_locale_engine));
				if (!isset($date['hour'])) $date['hour'] = '00';
				if (!isset($date['minute'])) $date['minute'] = '00';
				if (!isset($date['second'])) $date['second'] = '00';
				return "{$date['hour']}:{$date['minute']}:{$date['second']}";
			case 'integer':
				return Zend_Locale_Format::getInteger($value, array('locale'=>$this->_locale_engine));
			case 'float':
				if ($num_of_decimals === null) $num_of_decimals = 3;
				return Zend_Locale_Format::getFloat($value, array('precision'=>$num_of_decimals, 'locale'=>$this->_locale_engine));
			case 'decimal':
				if ($num_of_decimals === null) $num_of_decimals = 2;
				return Zend_Locale_Format::getFloat($value, array('precision'=>$num_of_decimals, 'locale'=>$this->_locale_engine));
		}

		return $value;
	}
	
	/**
	 * Clones and return the Zend_Locale engine
	 * @return Zend_Locale
	 */
	public function getLocaleEngine()
	{
		return clone $this->_locale_engine;
	}
	
	/**
	 * Clones and return the Zend_Translate engine
	 * @return Zend_Translate
	 */
	public function getTranslationEngine()
	{
		return clone $this->_translation_engine;
	}
	
	private function setFirstDayOfTheWeek()
	{
		$week_data = Zend_Locale_Data::getList($this->locale, 'week');
		switch ($week_data['firstDay']) {
			case 'mon':
				$daynumber = 1;
				break;
			case 'tue':
				$daynumber = 2;
				break;
			case 'wed':
				$daynumber = 3;
				break;
			case 'thu':
				$daynumber = 4;
				break;
			case 'fri':
				$daynumber = 5;
				break;
			case 'sat':
				$daynumber = 6;
				break;
			default:
				$daynumber = 0;
		}
		
		$this->first_day_of_the_week = $daynumber;
	}
	
	/**
	 * @return integer
	 */
	public function getFirstDayOfTheWeek()
	{
		return $this->first_day_of_the_week;
	}
	
	public function __wakeup()
	{
		Zend_Registry::set('Zend_Translate', $this->_translation_engine);
	}
}