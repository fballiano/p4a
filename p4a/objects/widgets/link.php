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
	 * "A" HTML tag.
	 * The link is built rendering a complete "A" tag,
	 * the {@link HREF} instead is only the href part of the "A" tag.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_LINK extends P4A_WIDGET
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
		function &link ($name, $id = NULL)
		{
			$prefix = 'lnk' ;
			
			if( $id === NULL ) {
				parent::p4a_widget($name, $prefix);
			} else {
				parent::p4a_widget($name, $prefix, $id);
			}
		}
		
		/**
		 * HTML rendered link.
		 * @return string
		 * @access public
		 */
		function getAsString()
		{
			if( $this->isEnabled() )
			{
				$header 		= '<A href="#" class="link" ';
				$close_header 	= '>';
				$footer			= '</A>';
				$sReturn  = $header . $this->composeStringProperties() . $this->composeStringActions() . $close_header;
				$sReturn .= $this->getValue();
				$sReturn .= $footer;
			}
			else
			{
				$sReturn = $this->getValue();
			}
			
			return $sReturn;  
		}
	}
?>