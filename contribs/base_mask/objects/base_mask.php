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
 * To contact the authors write to:								<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)											<br>
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
 * @package P4A_Base_Mask
 */
 
/**
 * A mask object with some basic elements
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_Base_Mask
 */
class P4A_Base_Mask extends P4A_Mask
{
	var $mandatory_fields = array();
	var $frame = null;
	var $warning = null;
	
	function P4A_Base_Mask()
	{
		parent::p4a_mask();
		$this->build("p4a_frame", "frame");
		$this->frame->setWidth(730);
		
		$this->build("p4a_message", "warning");
		$this->frame->anchorCenter($this->warning);
		
		$this->display("main", $this->frame);
	}
	
	function addMandatoryField($field_name)
	{
		$this->mandatory_fields[] = $field_name;
		$this->fields->$field_name->label->setStyleProperty("font-weight", "bold");
	}
	
	function checkMandatoryFields()
	{
		foreach ($this->mandatory_fields as $field) {
			$value = $this->fields->$field->getNewValue();
			if (empty($value)) {
				return false;
			}
		}
		
		return true;
	}
}

?>