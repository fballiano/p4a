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
 * The label is associated to an input field, do not use it otherwise
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Label extends P4A_Widget
{
	/**
	 * @var string
	 */
	protected $_tooltip = null;

	/**
	 * @param string $name Object identifier
	 * @param string $value
	 */
	public function __construct($name, $value = null)
	{
		parent::__construct($name);
		$this->setLabel($value);
	}

	/**
	 * Returns the HTML rendered label.
	 * This is done by building a SPAN, because with a SPAN you
	 * can trigger events such as onClick ect.
	 * Label is rendered only if the widget is visible.
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<span id='$id' class='hidden'></span>";
		}
		
		$css_classes = $this->getCSSClasses();
		$actions = $this->composeStringActions();

		$tooltip_text = $this->getTooltip();
		$tooltip_icon = '';
		if ($tooltip_text) {
			$tooltip_icon = '<img src="' . P4A_ICONS_PATH . '/16/status/dialog-information.png" class="p4a_tooltip_icon" alt="" />';
			$tooltip_text = "<div id='{$id}tooltip' class='p4a_tooltip'><div class='p4a_tooltip_inner'>{$tooltip_text}</div></div>";
			$actions .= " onmouseover='p4a_tooltip_show(this)' ";
			$css_classes[] = 'p4a_label_tooltip';
		}

		$css_classes = join(' ', $css_classes);
		return "<label id='{$id}' class='$css_classes' " . $this->composeStringProperties() . 
				"$actions>$tooltip_icon<span>" . __($this->getLabel()) . "</span>$tooltip_text</label>\n";
	}

	/**
	 * @param string $text
	 * @return P4A_Label
	 */
	public function setTooltip($text)
	{
		$this->_tooltip = $text;
		return $this;
	}

	/**
	 * @return string
	 */
	function getTooltip()
	{
		return $this->_tooltip;
	}
}