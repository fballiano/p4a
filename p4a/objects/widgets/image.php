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
	 * Use this whan you want to put an image in your application.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_IMAGE extends P4A_WIDGET
	{
		/**
		* The label position
		* @access public
		* @var string
		*/
		var $label_position = 'bottom';
		
		/**
		 * The class constructor.
		 * @param string	Object identifier.
		 * @param string	The absolute source path of the image.
		 * @access private
		 */
		function &p4a_image($name, $value = NULL )
		{
			parent::p4a_widget($name);
			if ($value !== NULL){
				$this->setValue($value);
			}
		}
		
		/**
		 * Sets label position.
		 * @param strig		bottom or right
		 * @access public
		 */
		function setLabelPosition($position = "bottom")
		{
			$this->label_position = $position;
		}
		
		/**
		 * Returns label position.
		 * @access public
		 */		
		function getLabelPosition()
		{
			return $this->label_position;
		}
		
		/**
		 * Sets image's source from icon set repository.
		 * @param strig		The image filename without extension (e.g.: "new").
		 * @access public
		 */
		function setIcon($icon)
		{
			$value = P4A_ICONS_PATH . '/' . $this->p4a->i18n->getLanguage() . '/' . $this->p4a->i18n->getCountry() . '/' . $icon . '.' . P4A_ICONS_EXTENSION ;
			$this->setValue($value);
		}
		
		/**
		 * Sets image's source from absolute url.
		 * @param strig		The image source url.
		 * @access public
		 */
		function setValue($value)
		{
			parent::setValue($value);
			$this->setProperty('src', $value);
		}
		

		/**
		 * Returns the HTML rendered label.
		 * @access public
		 */
		function getAsString()
		{
			if (! $this->isVisible()) {
				return NULL;
			}
			
			$label = $this->getLabel();
			$actions = $this->composeStringActions();
			$properties = $this->composeStringProperties();
			if ($label) {
				if ($this->getLabelPosition() == 'bottom') {
					$class = "dd_block";
				}else{
					$class = "dd_inline";
				}				
				$sReturn  = "<dl>";
				$sReturn .= "<dt><img $properties $actions /></dt>";
				$sReturn .= "<dd class=\"$class\">$label</dd>";
				$sReturn .= "</dl>\n";
			}else{
				$sReturn  = "<img $properties $actions />\n";
			}
			
			return $sReturn; 
		}
	}
?>
