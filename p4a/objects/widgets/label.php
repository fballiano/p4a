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
 * The label is associated to an input field, do not use it otherwise
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
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
			$tooltip_icon = '<img src="' . P4A_ICONS_PATH . '/16/info.png" class="p4a_tooltip_icon" alt="" />';
			$tooltip_text = "<div id='{$id}tooltip' class='p4a_tooltip'>{$tooltip_text}</div>";
			$actions .= " onmouseover='p4a_tooltip_show(this)' ";
			$css_classes[] = 'p4a_label_tooltip';
		}

		$css_classes = join(' ', $css_classes);
		return "<label id='{$id}' class='$css_classes' " . $this->composeStringProperties() . 
				"$actions>$tooltip_icon" . __($this->getLabel()) . "$tooltip_text</label>\n";
	}

	/**
	 * @param string $text
	 */
	public function setTooltip($text)
	{
		$this->_tooltip = $text;
	}

	/**
	 * @return string
	 */
	function getTooltip()
	{
		return $this->_tooltip;
	}
}