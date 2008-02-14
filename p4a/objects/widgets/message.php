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
 * To contact the authors write to:									<br />
 * CreaLabs SNC														<br />
 * Via Medail, 32													<br />
 * 10144 Torino (Italy)												<br />
 * Website: {@link http://www.crealabs.it}							<br />
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
	 * @return P4A_Message
	 */
	public function setValue($value = null)
	{
		$this->value = $value;
		return $this;
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
	 * @return P4A_Message
	 */
	public function setIcon($type = 'warning')
	{
		$this->icon = $type;
		return $this;
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
	 * @return P4A_Message
	 */
	public function setSize($size)
	{
		$this->size = $size;
		return $this;
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
	 * @return P4A_Message
	 */
	public function autoClear($enable = true)
	{
		$this->auto_clear = $enable;
		return $this;
	}
}