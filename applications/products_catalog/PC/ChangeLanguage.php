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

namespace PC;

use P4A\Mask\Base\Base;
use P4A\P4A;

/**
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class ChangeLanguage extends Base
{
    protected $i18n_dir = null;

    public function __construct()
    {
        parent::__construct();

        $this->i18n_dir = dirname(dirname(__FILE__)) . "/i18n";
        $current_locale = P4A::singleton()->i18n->getLocale();

        $languages = array();
        foreach (scandir($this->i18n_dir) as $file) {
            if (substr($file, -4) == ".php") {
                $locale = substr($file, 0, -4);
                list($language_code, $region_code) = explode("_", $locale);
                $language_name = \Zend_Locale_Data::getContent($current_locale, 'language', $language_code);
                $region_name = \Zend_Locale_Data::getContent($current_locale, 'country', $region_code);
                $languages[] = array("locale" => $locale, "description" => "{$language_name} ({$region_name})");
            }
        }

        $this->build("P4A\DataSource\ArraySource", "languages")
            ->setPk("locale")
            ->load($languages);

        $this->build("P4A\Widget\Field", "choose_language")
            ->setType("radio")
            ->setValue($current_locale)
            ->setSource($this->languages);
        $this->choose_language->label->setWidth(120);

        $this->build("P4A\Widget\Button", "apply")
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