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
 * @package p4a
 */

/**
 * A mask object with some basic elements
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */
class P4A_Base_Mask extends P4A_Mask
{
	private $required_fields = array();
	public $frame = null;

	public function __construct()
	{
		parent::__construct();
		$this->build("P4A_Frame", "frame");
		$this->frame->setWidth(730);

		$this->display("main", $this->frame);
	}

	public function setRequiredField($field_name)
	{
		$this->required_fields[] = $field_name;
		$this->fields->$field_name->label->setStyleProperty("font-weight", "bold");
	}

	public function checkRequiredFields()
	{
		$error = false;
		foreach ($this->required_fields as $field) {
			$value = $this->fields->$field->getNewValue();
			if (strlen($value) == 0) {
				$error = true;
				$value = $this->fields->$field->seterror();
			}
		}

		return !$error;
	}
	
	public function warning($message)
	{
		P4A::singleton()->message($message, 'warning');
	}
	
	public function error($message)
	{
		P4A::singleton()->message($message, 'error');
	}
	
	public function info($message)
	{
		P4A::singleton()->message($message, 'info');
	}
}