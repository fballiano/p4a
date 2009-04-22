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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

/**
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Progress_Bar extends P4A_Widget
{
	/**
	 * @var integer
	 */
	protected $_value = 0;

	/**
	 * @return integer
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param integer $value
	 * @return P4A_Progress_Bar
	 */
	public function setValue($value = 0)
	{
		if (!is_numeric($value)) {
			$value = 0;
		} elseif ($value > 100) {
			$value = 100;
		} elseif ($value < 0) {
			$value = 0;
		}
		
		$this->_value = round($value, 0);
		return $this;
	}
	
	/**
	 * Retuns the HTML rendered progress bar.
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();
		$class = $this->composeStringClass(array(
			"ui-progressbar",
			"ui-widget",
			"ui-widget-content",
			"ui-corner-all")
		);
		
		$return  = "<script style='text/javascript'>p4a_load_css(p4a_theme_path + '/jquery/ui.progressbar.css')</script>";
		$return .= "<div id='$id' $class $properties $actions><div style='width:{$this->_value}%' class='ui-progressbar-value ui-widget-header ui-corner-all'></div></div>";
		return $return;
	}
}