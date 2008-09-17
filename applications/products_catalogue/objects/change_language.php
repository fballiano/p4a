<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/agpl.html>.
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
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

/**
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class Change_Language extends P4A_Base_Mask
{
	protected $i18n_dir = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->frame->setWidth(300);
		
		$this->i18n_dir = dirname(dirname(__FILE__)) . "/i18n";
		$current_locale = P4A::singleton()->i18n->getLocale();
		
		$languages = array();
		foreach (scandir($this->i18n_dir) as $file) {
			if (substr($file, -4) == ".php") {
				$locale = substr($file, 0, -4);
				list($language_code, $region_code) = explode("_", $locale);
				$language_name = Zend_Locale_Data::getContent($current_locale, 'language', $language_code);
				$region_name = Zend_Locale_Data::getContent($current_locale, 'country', $region_code);
				$languages[] = array("locale"=>$locale, "description"=>"{$language_name} ({$region_name})");
			}
		}
		
		$this->build("P4A_Array_Source", "languages")
			->setPk("locale")
			->load($languages);
			
		$this->build("P4A_Field", "choose_language")
			->setType("radio")
			->setValue($current_locale)
			->setSource($this->languages);
		$this->choose_language->label->setWidth(120);
			
		$this->build("P4A_Button", "apply")
			->implement("onclick", $this, "apply");
		
		$this->frame
			->anchor($this->choose_language)
			->newRow()
			->anchorCenter($this->apply);
	}
	
	public function apply()
	{
		$new_locale = $this->choose_language->getNewValue();
		require "{$this->i18n_dir}/{$new_locale}.php";
		$p4a = P4A::singleton();
		$p4a->i18n->setLocale($new_locale);
		$p4a->i18n->mergeTranslation($msg);
		$p4a->showPrevMask(true);
	}
}