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
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
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
	 */
	public function setSize($size)
	{
		$this->_size = $size;
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
	 */
	public function setIcon($icon)
	{
		$this->_icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->_icon;
	}
	
	/**
	 * alias for setIcon()
	 * @param string $image
	 */
	public function setSource($source)
	{
		$this->_icon = $source;
	}
	
	/**
	 * alias for getIcon()
	 * @return string
	 */
	public function getSource()
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