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

class P4A_Message extends P4A_Widget
{
	/**
	 * @var string
	 */
	protected $icon = 'warning';
	
	/**
	 * @var integer
	 */
	protected $size = 48;
	
	/**
	 * @var boolean
	 */
	protected $auto_clear = true;
	
	/**
	 * @var string
	 */
	protected $value = null;

	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value = null)
	{
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		$return = '';
		if ($this->isVisible() and strlen($this->getValue())) {
			$classes = join(' ', $this->getCSSClasses());
			$properties = $this->composeStringProperties();
			$actions = $this->composeStringActions();
			$value = $this->getValue();
			$icon = $this->getIcon();
			$size = $this->getSize();

			if (strlen($icon)) {
				if (strpos($icon, '.') === false) {
					$icon = P4A_ICONS_PATH . "/$size/$icon." . P4A_ICONS_EXTENSION;
				}
				$icon = "<img src='$icon' alt='' />";
			}
			$return = P4A_Generate_Widget_Layout_Table($icon, $value, $classes, "id='$id' $properties $actions");
		}

		if ($this->auto_clear) {
			$this->setValue();
		}

		if (!strlen($return)) return "<span id='$id' class='hidden'></span>";
		return $return;
	}

	/**
	 * @param string $type
	 */
	public function setIcon($type = 'warning')
	{
		$this->icon = $type;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * @param integer $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}

	/**
	 * @return integer
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @param boolean $enable
	 */
	public function autoClear($enable = true)
	{
		$this->auto_clear = $enable;
	}
}