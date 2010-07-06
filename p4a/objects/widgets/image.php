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

/**
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class P4A_Image extends P4A_Widget
{
	/**
	 * @var string
	 */
	protected $_icon = '';
	
	/**
	 * @var integer
	 */
	protected $_size = 32;

	/**
	 * @param integer $size
	 * @return P4A_Image
	 */
	public function setSize($size)
	{
		$this->_size = $size;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getSize()
	{
		return $this->_size;
	}

	/**
	 * @param string $icon
	 * @return P4A_Image
	 */
	public function setIcon($icon)
	{
		$this->_icon = $icon;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->_icon;
	}

	/**
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<span id='$id' class='hidden'></span>";
		}

		$class = $this->composeStringClass();
		$actions = $this->composeStringActions();
		$properties = $this->composeStringProperties();
		$label = $this->getLabel();
		
		if (strpos($this->_icon, '.') !== false) {
			$icon = $this->_icon;
		} else {
			$icon = P4A_ICONS_PATH . "/{$this->_size}/{$this->_icon}";
			if (!$this->isEnabled()) $icon .= "_disabled";
			$icon .= '.' . P4A_ICONS_EXTENSION;
		}
		
		return "<img id='$id' src='$icon' alt='$label' $properties $actions $class />\n";
	}
}