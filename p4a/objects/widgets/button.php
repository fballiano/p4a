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
	 * HTML "button".
	 * It's useful to trigger actions in easy way (with/without graphics).
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_BUTTON extends P4A_WIDGET
	{
		/**
		* The icon used by button, if null standard html button is used.
		* @access public
		* @var string
		*/
		var $icon = NULL;

		/**
		 * Class constructor.
		 * @param string			Mnemonic identifier for the object.
		 * @param string			The icon taken from icon set (file name without extension).
		 * @access private
		 */
		function &p4a_button($name, $icon = NULL)
		{
			parent::p4a_widget($name);
			$this->addAction('onClick');
			$this->setDefaultLabel();
			if ($icon !== NULL){
				$this->setIcon($icon);
			}
		}
		
		/**
		 * Sets the label for the button.
		 * It'a a wrapper for set_value().
		 * @param string	The value
		 * @access public
		 */
		function setLabel($value)
		{
			$this->setValue( $value );
		}
		
		/**
		 * Returns the label for the button.
		 * It'a a wrapper for get_value().
		 * @access public
		 * @return string
		 */
		function getLabel()
		{
			return $this->getValue();
		}
		
		/**
		 * Sets the value for the button.
		 * Also sets the right HTML property for correct display.
		 * @param string	The value
		 * @access public
		 */
		function setValue($value)
		{
			parent::setValue($value);
			$this->setProperty( 'value', $value );
		}
		
		/**
		 * Sets the icon for the button.
		 * @param string		The icon taken from icon set (file name without extension).
		 * @access public
		 */
		function setIcon($icon)
		{
			$this->icon = $icon;
		}
		
		/**
		 * Returns the icon for the button.
		 * @access public
		 * @return string
		 */
		function getIcon()
		{
			return $this->icon;
		}

		/**
		 * Retuns the HTML rendered button.
		 * @access public
		 * @return string
		 */		
		function getAsString()
		{
			$p4a =& P4A::singleton();
			
			if (! $this->isVisible()) {
				return NULL;
			}
			$header = '' ;
			
			$enabled = $this->isEnabled();
			
			if ($this->icon != NULL)
			{
				if( $enabled ) {
					$header .= '<a class="link_button" href="#">';
				}
				
				$header .= '<img class="' ;
				
				if( $enabled ) {
					$header .= 'clickable img_button ';
				}
				
				$header .= '" src="' . P4A_ICONS_PATH . '/' . $p4a->i18n->getLanguage() . '/' . $p4a->i18n->getCountry() . '/' . $this->icon . '.' . P4A_ICONS_EXTENSION . '" alt="' . $p4a->i18n->messages->get($this->icon) . '" ';
				  
				$footer = ' />';
				
				if( $enabled ) {
					$footer .= '</a>';
				}
				
				$footer .= "\n";
			}else{
				$header .= '<INPUT type="button" class="' ;
				if( $enabled ) {
					$header .= 'clickable ';  
				}
				$header .= 'border_box font4 no_print" ';
				if( !$enabled ) {
					$header .= ' disabled="disabled"';
				}
				$footer = ' />' . "\n";
			}
			
			$sReturn = $header . $this->composeStringProperties() ;
			if( $enabled ) {
				$sReturn .= $this->composeStringActions();
			}
			$sReturn .= $footer;
			
			return $sReturn;
		}
	}		
?>
