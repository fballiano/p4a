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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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
	 * p4a error management class.
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_ERROR
	{
		var $message = NULL;
		var $data = array();
		var $object_id = NULL;
		var $external_object = NULL;
		var $backtrace = array();

		/**
		 * Class constructor.
		 */
		function &p4a_error($message=NULL, $object=NULL, $external_object=NULL)
		{
			$this->data['class']	= NULL;
			$this->data['function'] = NULL;
			$this->data['file']		= NULL;
			$this->data['line']		= NULL;
			$this->data['type']		= NULL;
			$this->data['args']		= array();

			if(function_exists('debug_backtrace')) {
				$this->backtrace = debug_backtrace();
				array_shift($this->backtrace);

				$this->data = $this->backtrace[0];
			}

			$this->message = $message;

			if (is_object($object)) {
				$this->object_id = $object->getID();
			}

			$this->external_object = $external_object;
		}

		function getMessage()
		{
			return $this->message;
		}

		function getBacktrace()
		{
			return $this->backtrace;
		}

		function getData()
		{
			return $this->data;
		}

		function getObjectId()
		{
			return $this->object_id;
		}

		function getExternalObject()
		{
			return $this->external_object;
		}
	}

?>
