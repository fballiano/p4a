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
	 * A toolbar is a buttons/images set.
	 * Every button/image can have an action handler.
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_TOOLBAR extends P4A_WIDGET
	{
		/**
		 * Buttons collection.
		 * @var array
		 * @access public
		 */
		var $buttons = array();
		
		/**
		 * Buttons counter.
		 * @var integer
		 * @access private
		 */
		var $index = 0;
		
		/**
		 * Separators counter.
		 * @var integer
		 * @access private
		 */
		var $index_separator = 0;
		
		/**
		 * Class costructor.
		 * @param string				Mnemonic identifier for the object.
		 * @param mask					The mask on wich the toolbar will operate.
		 * @access private
		 */
		function &p4a_toolbar($name)
		{
			parent::p4a_widget($name);
			$this->setOrientation('horizontal');
			$this->setProperty('cellpadding', '0');
			$this->setProperty('cellspacing', '0');
			$this->setProperty('border', '0'); 
		}
		
		/**
		 * Adds a button/image object to the toolbar.
		 * @param object object			The button/image object.
		 * @access public
		 */
		function addButton(&$button, $name)
		{
			$this->index++;
			$this->buttons->$name =& $button;
		}
		
		/**
		 * Istances a new p4a_button object and than adds it to the toolbar.
		 * @param string			Mnemonic identifier for the object.
		 * @param string			The icon taken from icon set (file name without extension).
		 * @access public
		 * @see BUTTON
		 */
		function newButton($button_name, $icon = NULL)
		{
			return $this->buttons->build("p4a_button", $button_name, $icon);
		}
		
		/**
		 * Adds a separator image.
		 * @access public
		 */
		function addSeparator()
		{
			$this->index++;
			$this->index_separator++;
			
			$image =& $this->buttons->build("P4A_Image", 's' . $this->index_separator++);
			$image->setIcon('separator');
		}
		
		/**
		 * Adds a spacer image of the desidered width.
		 * @param integer		Width in pixel from the spacer.
		 * @access public
		 */
		function addSpace( $width = 10 )
		{
			$this->index++;
			$this->index_separator++;
			$image =& $this->buttons->build("P4A_Image", 's' . $this->index_separator++);
			$image->setIcon('spacer');
			$image->setWidth($width);
		}
		
		/**
		 * Turns off the action handler for the desidered button.
		 * @param string		Button identifier.
		 * @access public
		 */
		function disable($button_name = NULL)
		{
			if ($button_name === NULL)
			{
				foreach(array_keys($this->buttons) as $button_name)
				{
					$this->buttons[$button_name]->disable();
				}
			}
			else
			{
				if (array_key_exists($button_name, $this->buttons))
				{
					$this->buttons[$button_name]->disable();
					//ERROR
				}
			}			
		}
		
		/**
		 * Turns on the action handler for the desidered button.
		 * @param string		Button identifier.
		 * @access public
		 */
		function enable($button_name = NULL)
		{
			if ($button_name === NULL)
			{
				foreach(array_keys($this->buttons) as $button_name)
				{
					$this->buttons[$button_name]->enable();
				}
			}
			else
			{
				if (array_key_exists($button_name, $this->buttons))
				{
					$this->buttons[$button_name]->enable();
					//ERROR
				}
			}			
		}		
		
		/**
		 * Sets the rendering orientation for the toolbar.
		 * @param string		Orientation (horizontal|vertical).
		 * @access public
		 */
		function setOrientation($orientation)
		{
			$this->orientation = $orientation;
			$this->sheet = NULL;
		}
		
		/**
		 * Returns the HTML rendered widget.
		 * @return string
		 * @access public
		 */
		function getAsString()
		{
			if (!$this->isVisible()) {
				return '';
			}
			
			$header = '<table class="toolbar" ';
			$close_header = ' >';
			$contents = '';
			$footer = '</table>';
        	if ($this->orientation == 'vertical')
        	{
        		foreach(array_keys($this->buttons) as $button_name)
        		{
        			if (is_object($this->buttons[$button_name]))
        			{
        				$contents .= '<tr><td>' . $this->buttons[$button_name]->getAsString() . '</td></tr>' . "\n";
        			}
        		}
        	}
        	else
        	{
        		$contents .= '<tr>';
        		foreach(array_keys($this->buttons) as $button_name)
        		{
        			if (is_object($this->buttons[$button_name]))
        			{
        				$contents .= '<td>' . $this->buttons[$button_name]->getAsString() . '</td>' . "\n";
        			}
        		}
        		$contents .= '</tr>';
        	}
			
			return $header . $this->composeStringProperties() . $close_header . $contents . $footer ; 
		}
	}		
?>