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
	 * Line
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Line extends P4A_Widget
	{
		/**
		 * Class constructor.
		 * You can specify an object ID if you want to have the same
		 * object with always the same ID. This is useful especially
		 * for web sites (to allow bookmarking and correct spidering).
		 * @param string		Mnemonic identifier for the object.
		 * @param string		Object ID, if not specified will be generated.
		 * @access private
		 */
		function P4A_Line($name, $id = NULL)
		{
			$prefix = 'line';

			if ($id === NULL) {
				parent::P4A_Widget($name, $prefix);
			} else {
				parent::P4A_Widget($name, $prefix, $id);
			}
		}

		/**
		 * HTML rendered link.
		 * @return string
		 * @access public
		 */
		function getAsString()
		{
			if (!$this->isVisible()) {
				return null;
			}

			$header 		= '<hr ' .$this->getId(). 'noshade class="color2" ';
			$close_header 	= '/>';
			$sReturn  = $header . $this->composeStringProperties() . $this->composeStringActions() . $close_header;

			return $sReturn;
		}
	}
?>