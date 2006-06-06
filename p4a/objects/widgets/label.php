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
	 * The label is associated to an input field, do not use it otherwise.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Label extends P4A_Widget
	{
		/**
		 * Tells if the fields content is formatted or not.
		 * @var string
		 * @access private
		 */
		var $formatted = true;

		/**
		 * The formatter class name for the data field.
		 * @var string
		 * @access private
		 */
		var $formatter_name = NULL;

		/**
		 * The format name for the data field.
		 * @var string
		 * @access private
		 */
		var $format_name = NULL;

		/**
		 * The class constructor
		 * @param string	Object identifier.
		 * @param string	The Value of the label.
		 * @access private
		 */

		function P4A_Label($name, $value=NULL)
		{
			parent::P4A_Widget($name);
			$this->setLabel($value);
		}

		/**
		 * Only returns the value of the label.
		 * @return string
		 * @access public
		 */
		function getValue()
		{
			return $this->getLabel();
		}

		/**
		 * Only returns the value of the label.
		 * @param string Label's value.
		 * @access public
		 */
		function setValue($value=NULL)
		{
			$this->setLabel($value);
		}

		/**
		 * Returns the HTML rendered label.
		 * This is done by building a SPAN, because with a SPAN you
		 * can trigger events such as onClick ect.
		 * Label is rendered only if the widget is visible.
		 * @param string Label's value.
		 * @access public
		 */
		function getAsString()
		{
			if (!$this->isVisible()) {
				return '';
			}

			$id = $this->getId();
			$header	= "<label id='{$id}' class='label' ";
			$close_header = ">";
			$footer	= "</label>\n";

			$string = $header . $this->composeStringProperties() .
						$this->composeStringActions() .
						$close_header . $this->getLabel() . $footer;

			return $string;
		}

		/**
		 * Set the label type, normal or temporary
		 * @param string label type.
		 * @access public
		 */
		function setType($type = 'normal')
		{
			$this->type = $type;
		}

		/**
		 * Returns the label type
		 * @access public
		 */
		function getType()
		{
			return $this->type;
		}
	}
?>
