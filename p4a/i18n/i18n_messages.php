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
	 * p4a internationalization class for string messages.
	 *
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_I18N_Messages
	{
		/**
		 * All messages are stored here.
		 * @access private
		 * @var array
		 */
		var $messages = array();

		/**
		 * Class constructor.
		 * @param string				The desired language.
		 * @param string				The desired country.
		 * @param string				Optional the desired codepage.
		 * @access private
		 */
		function &P4A_I18N_Messages($language, $country, $codepage = NULL)
		{
			$codepage = ($codepage ? ".$codepage" : "");
			$msg_file = "{$language}/{$country}{$codepage}.php";
			include(dirname(__FILE__) . "/messages/{$msg_file}");

			$application_localization = P4A_APPLICATION_LOCALES_DIR . "/{$msg_file}";

			if (file_exists($application_localization)) {
				include($application_localization );
			}

			$this->messages = $messages;
			unset($messages);
		}

		/**
		 * Retrieves a message.
		 * @access public
		 * @param string		The first level message id (default).
		 * @param string		The second level message id (used only when the first level value is an array. Eg: days names).
		 * @return string
		 */
		function get($first_level_id, $second_level_id = NULL)
		{
			if ($second_level_id === NULL) {
				if (array_key_exists($first_level_id, $this->messages)){
					return $this->messages[$first_level_id];
				} else {
					return "";
				}

			} else {
				return $this->messages[$first_level_id][$second_level_id];
			}
		}
	}

?>