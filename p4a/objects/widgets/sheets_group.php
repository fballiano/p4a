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
	 * A group of sheet will help you having clean complex masks.
	 *
	 * We render the sheet group as a "current" sheet with a tabbed
	 * navigation system between sheets.
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_SHEETS_GROUP extends P4A_WIDGET
	{
		/**
		 * Here we store all sheets.
		 * @var array
		 * @access public
		 */
		var $sheets = array();
		
		/**
		 * Here we store all labels (with the same name of the sheet).
		 * @var array
		 * @access public
		 */
		var $labels = array();
		
		/**
		 * Maps sheet_name=>position
		 * @var array
		 * @access private
		 */
		var $sheets_map = array();
		
		/**
		 * Maps position=>sheet_name
		 * @var array
		 * @access private
		 */
		var $positions_map = array();
		
		/**
		 * Currently shown sheet.
		 * @var array
		 * @access private
		 */
		var $active_sheet = NULL;
		
		/**
		 * Sheets navigation bar.
		 * @var array
		 * @access private
		 */
		var $navigation_bar = NULL;
		
		/**
		 * Sheets type.
		 * @var string
		 * @access private
		 */
		var $type = 'normal';
		
		/**
		 * Class constructor.
		 * Sets default template and instances the sheet navigation bar.
		 * @param string	The name of the sheet
		 * @access private
		 */
		function &p4a_sheets_group( $name )
		{
			parent::p4a_widget( $name ) ;
			$this->useTemplate( 'sheets_group_gray' );
			$this->build("p4a_sheets_group_navigation_bar", "sheets_group_navigation_bar");
		}
		
		/**
		 * Adds a sheet to the group.
		 * Creates a clickable label for sheet switching.<br>
		 * To disable a sheet use: sheet_group->labels[sheet_name]->disable().
		 * @param sheet		The sheet we're adding
		 * @param integer	The position (from 1).
		 * @access public
		 */
		function addSheet( &$sheet, $name, $position = NULL )
		{
			$this->sheets->$name =& $sheet;
			$this->setPosition( $sheet->name, $position );
			
			$this->labels->build("P4A_Link", $name);
			$this->labels->{$sheet->name}->setValue($sheet->getLabel());
			
			$this->intercept($sheet, 'set_label', 'setSheetLabel');
			$this->labels->{$sheet->getName()}->addAction('onClick');
 			$this->intercept($this->labels->{$sheet->getName()}, 'onClick', 'labelsOnClick');
			
			if( $this->active_sheet === NULL )
			{
				$this->setActive( $sheet->name );
			}
		}
		
		/**
		 * Creates a new p4a_sheet and add it to the group.
		 * @param string	Mnemonic identifier for the object.
		 * @param integer	The position (from 1).
		 * @access public
		 */
		function newSheet( $name, $position = NULL )
		{
			$this->addSheet( new p4a_sheet($name), $position );
		}
		
		/**
		 * Sets the position for a sheet.
		 * @param string	Mnemonic identifier for the object.
		 * @param integer	The position (from 1), if null appends the sheet at the end.
		 * @access public
		 */
		function setPosition( $sheet_name, $position = NULL )
		{
			if( in_array( $position, array_values( $this->sheets_map ) ) )
			{
				error('existing position');
			}
			
			if( $position === NULL )
			{
				$new_position = $this->getFreePosition() ;
				$this->sheets_map[ $sheet_name ] = $new_position ;
				$this->positions_map[ $new_position ] = $sheet_name ;
			}
			else
			{
				$this->sheets_map[ $sheet_name ] = $position ;
				$this->positions_map[ $position ] = $sheet_name ;
			}
		}
		
		/**
		 * Returns the index of the next free position (at the end).
		 * @access public
		 */
		function getFreePosition()
		{
			return ( sizeof( $this->positions_map ) + 1 ) ;
		}
		
		/**
		 * Sets a sheet as active (by name).
		 * The currently active sheet is available in $sheet_group->active_sheet.
		 * @param string		Mnemonic identifier for the object.
		 * @access public
		 * @see $active_sheet
		 */
		function setActive( $sheet_name )
		{
			if( $this->active_sheet !== NULL )
			{
				if( $this->labels->{$this->active_sheet->getName()} !== NULL )
				{
					$this->labels->{$this->active_sheet->getName()}->enable();
					unset( $this->active_sheet ) ;
				}
			}
			
			$this->active_sheet =& $this->sheets[ $sheet_name ];
			$this->labels[ $sheet_name ]->disable();
		}
		
		/**
		 * Sets a sheet as active (by position).
		 * The currently active sheet is available in $sheet_group->active_sheet.
		 * @param integer		The position.
		 * @access public
		 * @see $active_sheet
		 */
		function setActivePosition( $position )
		{
			$sheet_name = $this->positions_map[ $position ];
			$this->setActive($sheet_name);
		}

		/**
		 * Sets the type of the widget.
		 * @access public
		 * @param string	 The type (normal: standard, modal: hides border and labels)
		 */
		function setType($type = 'normal')
		{
			$this->type = $type;
			if ($type == 'modal') {
				$this->setStyleProperty('border-style', 'none');
			}else{
				$this->setStyleProperty('border-style', 'solid');
			}
		}
		
		/**
		 * Sets the first sheet as active.
		 * @access public
		 */
		function moveFirst()
		{
			$this->setActivePosition(1);
		}
		
		/**
		 * Sets the previous sheet as active.
		 * @access public
		 */
		function movePrev()
		{
			if( $this->sheets_map[ $this->active_sheet->name ] > 1 )
			{
				$this->setActivePosition( ( $this->sheets_map[ $this->active_sheet->name ] - 1 ) ) ;
			}
		}
		
		/**
		 * Sets the next sheet as active.
		 * @access public
		 */
		function moveNext()
		{
			if( $this->sheets_map[ $this->active_sheet->name ] < sizeof( $this->sheets_map ) )
			{
				$this->setActivePosition( ( $this->sheets_map[ $this->active_sheet->name ] + 1 ) ) ;
			}
		}
		
		/**
		 * Sets the last sheet as active.
		 * @access public
		 */
		function moveLast()
		{
			$this->setActivePosition( sizeof( $this->positions_map ) );
		}
		
		/**
		 * Prepares all the labels variables for the template engine.
		 * @return array
		 * @access private
		 */
		function prepareLabels()
		{
			$items = array() ;
			$visible_counter = 0 ;
			
			for( $i = 1, $j = 1; $i <= sizeof( $this->positions_map ); $i++ )
			{
				if( $this->labels[ $this->positions_map[ $i ] ]->isVisible() )
				{
					$item = array();
					$item['label'] = $this->labels[ $this->positions_map[ $i ] ]->getAsString();
					$item['visible'] = $this->labels[ $this->positions_map[ $i ] ]->isVisible();
					
					if( $item['visible'] ) {
						$visible_counter++;
					}
					
					// Setting element activation state
					if( $this->sheets[ $this->positions_map[ $i ] ]->name == $this->active_sheet->name )
					{
						if ($i>1)
						{
							$items[($j-1)]['next_active'] = true;		
						}
						
						$item['active'] = true;
					}
					else
					{
						$item['active'] = false;
					}
					
					$items[$j] = $item;
					$j++;
				}
			}
			
			return array($items, $visible_counter);
		}

		/**
		 * Sets the sheet's label .
		 * The currently active sheet is available in $sheet_group->active_sheet.
		 * @param object		The sheet.
		 * @param string		The label.
		 * @access private
		 */
		function setSheetLabel(&$sheet, $label)
		{
			$this->labels->{$sheet->getName()}->setValue($label);
		}
		
		/**
		 * Action handler for the onClick action on labels.
		 * Sets active the sheet associated with the label.
		 * @access private
		 */
		function labelsOnClick()
		{
			$p4a =& P4A::singleton();
			$this->setActive($p4a->active_object->name);
		}
		
		/**
		 * Returns HTML rendered sheet_group.
		 * @return string
		 * @access private
		 */
		function getAsString()
		{
			if( $this->active_sheet === NULL )
			{
				$this->setActivePosition(1);
			}
			
			if( $this->navigation_bar->isVisible() )
			{
				foreach($this->navigation_bar->getDisplayVars() as $key=>$value){
					$this->display($key, $value);
				}
			}
			$labels_data = $this->prepareLabels();
			
			$this->display('type', $this->type);
			$this->display( 'properties', $this->composeStringProperties() );
			$this->display( 'items', $labels_data[0] );
			$this->display( 'sheet_colspan', ( ($labels_data[1] * 2) + 1 ) );
			$this->display( 'sheet', $this->active_sheet );
			return $this->fetchTemplate();
		}
	}
	
	/**
	 * Navigation bar for sheets_groups.
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_SHEETS_GROUP_NAVIGATION_BAR extends P4A_WIDGET
	{
		/**
		 * Button for 'first' movement.
		 * @var link
		 * @access public
		 */
		var $button_first = NULL ;
		
		/**
		 * Button for 'previous' movement.
		 * @var link
		 * @access public
		 */
		var $button_prev = NULL ;
		
		/**
		 * Button for 'next' movement.
		 * @var link
		 * @access public
		 */
		var $button_next = NULL ;
		
		/**
		 * Button for 'last' movement.
		 * @var link
		 * @access public
		 */
		var $button_last = NULL ;
		
		/**
		 * Class constructor.
		 * Instances all the buttons.
		 * By default is hidden.
		 * @param string				Mnemonic identifier for the object.
		 * @access private
		 */
		function &p4a_sheets_group_navigation_bar( $name = 'navigation_bar' )
		{
			parent::p4a_widget($name);
			
			$this->buid("P4A_Button", "button_first", "little_first");
			$this->button_first->setValue('<<');
			$this->button_first->addAction('onClick');
			$this->intercept($this->button_first, 'onClick', 'firstOnClick');
			
			$this->buid("P4A_Button", "button_prev", "little_prev");
			$this->button_prev->setValue('<');
			$this->button_prev->addAction('onClick');
			$this->intercept($this->button_prev, 'onClick', 'prevOnClick');
			
			$this->buid("P4A_Button", "button_next", "little_next");
			$this->button_next->setValue('>');
			$this->button_next->addAction('onClick');
			$this->intercept($this->button_next, 'onClick', 'nextOnClick');
			
			$this->buid("P4A_Button", "button_last", "little_last");
			$this->button_last->setValue('>>');
			$this->button_last->addAction('onClick');
			$this->intercept($this->button_last, 'onClick', 'lastOnClick');
			
			$this->setInvisible();
		}
		
		/**
		 * Action handler for "first" button click.
		 * @access public
		 */
		function firstOnClick()
		{
			$this->parent->moveFirst();
		}
		
		/**
		 * Action handler for "previous" button click.
		 * @access public
		 */
		function prevOnClick()
		{
			$this->parent->movePrev();
		}
		
		/**
		 * Action handler for "next" button click.
		 * @access public
		 */
		function nextOnClick()
		{
			$this->parent->moveNext();
		}

		/**
		 * Action handler for "last" button click.
		 * @access public
		 */
		function lastOnClick()
		{
			$this->parent->moveLast();
		}
		
		/**
		 * Prepares variables for the template engine.
		 * @return array
		 * @access private
		 */
		function getDisplayVars()
		{
			$array_return['navigation_bar_visible'] = $this->isVisible();
			$array_return['button_first'] = $this->button_first->getAsString();
			$array_return['button_prev'] = $this->button_prev->getAsString();
			$array_return['button_next'] = $this->button_next->getAsString();
			$array_return['button_last'] = $this->button_last->getAsString();
			return $array_return;
		}
	}

?>