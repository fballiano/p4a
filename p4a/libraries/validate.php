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
 * Data validation class
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Validate
{
	function _getData($data)
	{
		if (is_object($data)) {
			if (is_a($data, 'P4A_Field')) {
				return $data->data_field->getNewValue();
			} elseif (is_a($data, 'P4A_Data_Field')) {
				return $data->getNewValue();
			} else {
				P4A_Error("P4A_Validate: only p4a_field or p4a_data_field are allowed");
			}
		}
	}

	function notEmpty($data)
	{
		$data = P4A_Validate::_getData($data);
		return strlen($data) > 0;
	}

	function date($data)
	{
		$data = P4A_Validate::_getData($data);
		return Validate::date($data);
	}

	function email($data)
	{
		$data = P4A_Validate::_getData($data);
		return Validate::email($data);
	}

	function number($data)
	{
		$data = P4A_Validate::_getData($data);
		return Validate::number($data);
	}

	function string($data)
	{
		$data = P4A_Validate::_getData($data);
		return Validate::string($data);
	}

	function uri($data)
	{
		$data = P4A_Validate::_getData($data);
		return Validate::uri($data);
	}
}