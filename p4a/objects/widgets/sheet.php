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
 * Sheets is the way to manage layouts in MerliWork masks.
 *
 * A sheet is rendered as an HTML table, so it is
 * divided into cells (SHEET_CELL), every sheel cell
 * has all HTML capabilities such as rowspan, colspan
 * and stylesheets.
 * @author Fabrizio Balliano
 * @package p4a
 */
class P4A_Sheet extends P4A_Widget
{
	/**
	 * The grid, were all cells are stored.
	 * @access public
	 * @var array
	 */
	var $grid = array() ;

	/**
	 * True if the grid is already defined.
	 * @access private
	 * @var boolean
	 */
	var $grid_defined = false ;

	/**
	 * The number of rows in the sheet.
	 * @access private
	 * @var integer
	 */
	var $rows = 0 ;

	/**
	 * Stores infos about sheet rows.
	 * These infos are: occupational state etc...
	 * @access private
	 * @var array
	 */
	var $rows_infos = array() ;

	/**
	 * The number of columns in the sheet.
	 * @access private
	 * @var integer
	 */
	var $cols = 0 ;

	/**
	 * The index of the last occupied row.
	 * @access private
	 * @var integer
	 */
	var $last_occupied_row = 0 ;

	/**
	 * The default align for sheet cells
	 * @access private
	 * @var string
	 */
	var $default_align = 'left' ;

	/**
	 * The default vertical align for sheet cells
	 * @access private
	 * @var string
	 */
	var $default_valign = 'top' ;

	/**
	 * Sheet construction does nothing but calling his parent constructor.
	 * @param string	The name of the sheet
	 */
	function P4A_Sheet($name)
	{
		parent::P4A_Widget($name);
		$this->setDefaultLabel();
		$this->setProperty('cellpadding', '0');
		$this->setProperty('cellspacing', '0');
	}

	/**
	 * Defines and istance all the sheet cells.
     * This operation is the same that creating an HTML
     * table, so the entities we have are: SHEET, ROWS, COLS, CELLS.
	 * @access public
	 * @param integer	The number of rows of the grid.
	 * @param integer	The number of columns of the grid.
	 */
	function defineGrid($rows = 1, $cols = 1)
	{
		if ($cols == 0) {
			$cols = 1;
		}

		$this->setNumOfCols($cols);
		$this->addRow($rows);

		$this->grid_defined = true;
	}

	/**
	 * Anchors a widget to a sheet cell in the grid.
	 * In every cell there can be ONLY ONE widged.
	 * Only the $widget param is necessary, by defaut
	 * we'll search for the first free row in sheet and,
	 * if not found, istance a new row.
	 * If the grid is not yet defined than we'll define it 1x1 cells.
	 * Default spanning is always equal to 1: 1 widget in
	 * one grid cell.
	 * @access public
	 * @param widget		The widget to be anchored.
	 * @param integer		The row of the anchoration.
	 * @param integer		The column of the anchoration.
	 * @param integer		The number of rows the widget must occupy.
	 * @param integer		The number of colums the widget must occupy.
	 * @return sheet_cell	The cells where the widget has been anchored.
	 */
	function &anchor(&$widget, $row = NULL, $col = 1, $rowspan = 1, $colspan = 1)
	{
		if (!$this->isGridDefined()) {
			$this->defineGrid();
		}

		if ($row === NULL) {
			$row = $this->getFreeRow() ;
		} else {
			if ($row > $this->getNumOfRows()) {
				$this->addRow(($row - $this->getNumOfRows()));
			} elseif ($this->grid[$row][$col]->isOccupied()) {
				P4A_Error('"' . $this->getName() . '": Unable to anchor object "' . $widget->getName() . '" on row ' . $row . ' and col ' . $col . '.');
			}
		}

		if ($rowspan == 0) {
			$rowspan = $this->getNumOfRows() - $row + 1 ;
		}

		if ($colspan == 0) {
			$colspan = $this->getNumOfCols() - $col + 1 ;
		}

		for ($rowcounter = $row; $rowcounter < ($row + $rowspan); $rowcounter++) {
			for ($colcounter = $col; $colcounter < ($col + $colspan); $colcounter++) {
				if ($this->grid[$rowcounter][$colcounter]->isVisible()) {
					$this->grid[$rowcounter][$colcounter]->setInvisible();
				} else {
					P4A_Error('"' . $this->getName() . '": Unable to anchor object "' . $widget->getName() . '" on row ' . $row . ' and col ' . $col . '.');
				}
			}

			$this->setRowOccupied($rowcounter);
		}

		$this->grid[$row][$col]->setVisible();
		$this->grid[$row][$col]->anchor($widget, $rowspan, $colspan);
		$this->setRowOccupied($row);

		return $this->grid[$row][$col];
	}

	/**
	 * Anchors a text string to a sheet cell in the grid.
	 * @access public
	 * @param string		The string to be anchored.
	 * @param integer		The row of the anchoration.
	 * @param integer		The column of the anchoration.
	 * @param integer		The number of rows the widget must occupy.
	 * @param integer		The number of colums the widget must occupy.
	 * @return sheet_cell	The cells where the widget has been anchored.
	 * @see anchor()
	 */
	function &anchorText($text, $row = NULL, $col = 1, $rowspan = 1, $colspan = 1)
	{
		return $this->anchor($text, $row, $col, $rowspan, $colspan);
	}

	/**
	 * Adds a row to the sheet and sets it as occupied.
	 * During rendering phase this will generate a blank row.
	 * Returns the first cell of the row so you can use it
	 * in any way such as setting height.
	 * @return sheet_cell
	 */
	function &blankRow()
	{
		$row = $this->addRow();
		$this->setRowOccupied($row);
		return $this->grid[$row][1];
	}

	/**
	 * Reinizialize the span values for a sheet cell.
	 * For semplicity this is done by setting free the cell
	 * and than re-anchoring the widget with the new
	 * attributes.
	 * @param integer	The row index of the desidered cell.
	 * @param integer	The column index of the desidered cell.
	 * @param integer	The new rowspan for the desidered cell.
	 * @param integer	The new colspan for the desidered cell.
	 */
	function respan($row, $col, $rowspan = 1, $colspan = 1)
	{
		$widget =& $this->grid[$row][$col]->widget;
		$this->setFree($row, $col);
		$this->anchor($widget, $row, $col, $rowspan, $colspan);
	}

	/**
	 * Frees a cell.
	 * This method sets free a cell in the sheet resetting his
	 * visibility, rowspan and colspan attributes.
	 * This is done by calling the method set_free (internal of the sheet cell) for
	 * every cell that the one we are setting free occupied.
	 * @access public
	 * @param integer The row index.
	 * @param integer The column index.
	 */
	function setFree($row, $col)
	{
		$rowspan = $this->grid[$row][$col]->getProperty('rowspan');
		$colspan = $this->grid[$row][$col]->getProperty('colspan');

		for ($rowcounter = $row; $rowcounter < ($row + $rowspan); $rowcounter++) {
			for ($colcounter = $col; $colcounter < ($col + $colspan); $colcounter++) {
				$this->grid[$rowcounter][$colcounter]->setFree();
				$this->grid[$rowcounter][$colcounter]->setVisible();
			}

			if ($this->checkRowOccupied($rowcounter)) {
				$this->setRowOccupied($rowcounter);
			} else {
				$this->setRowFree($rowcounter);
			}
		}

		$this->grid[$row][$col]->setProperty('rowspan', 1);
		$this->grid[$row][$col]->setProperty('colspan', 1);
	}

	/**
	 * Returns the occupational state of the desidered row.
	 * The check is done only reading the internal occupation
	 * map so it is extremely fast.
	 * @access public
	 * @param integer	The desired row
	 * @return boolean	Occupational state of the desidered row
	 */
	function isRowOccupied($row)
	{
		return $this->rows_infos[$row]['occupied'];
	}

	/**
	 * Returns the occupational state of the desidered row.
	 * The check is done scanning the row to find if a row
	 * is really occupied. This is slower than is_row_occupied
	 * but sometimes is necessary for internal use.
	 * We think you should use is_row_occupied.
	 * @access private
	 * @param integer	The desired row
	 * @return boolean	Occupational state of the desidered row
	 */
	function checkRowOccupied($row)
	{
		for ($colcounter = 1; $colcounter <= sizeof($this->grid[$row]); $colcounter++) {
			if ($this->grid[$row][$colcounter]->isOccupied()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Return the index of the first free row in sheet.
	 * If there is a free row in the sheet returns its index
	 * else istance a new row a returns its index.
	 * @access public
	 * @return integer	The index of the first free row in sheet.
	 */
	function getFreeRow()
	{
		if (!$this->isGridDefined()) {
			$this->defineGrid();
		}

		$row = $this->getLastOccupiedRow();
		$row++;

		if ($row > $this->rows) {
			$row = $this->addRow();
		}

		return $row;
	}

 	/**
	 * Add one or more rows to the sheet.
	 * @access public
	 * @param integer	The number of rows you want to add.
	 * @return integer	The index of the first row.
	 */
	function addRow( $rows = 1 )
	{
		if ($rows > 0) {
			$row_pointer = $this->rows + 1;
		} else {
			$row_pointer = $this->rows;
		}

		$cols = $this->getNumOfCols();
		if ($cols == 0) {
			$this->setNumOfCols(1);
			$cols = 1;
		}

		for ($row_counter = $row_pointer; $row_counter <= ( $row_pointer + $rows ); $row_counter++) {
			$this->grid[ $row_counter ] = array() ;
			$this->rows_infos[ $row_counter ] = array() ;
			$this->rows_infos[ $row_counter ][ 'occupied' ] = false ;

			for( $cols_counter = 1; $cols_counter <= $cols; $cols_counter++ ) {
				$cell =& $this->build("p4a_sheet_cell", $row_counter . '_' .  $cols_counter);
				$this->grid[ $row_counter ][ $cols_counter ] =& $cell;
				$this->grid[ $row_counter ][ $cols_counter ]->setProperty('align', $this->default_align);
				$this->grid[ $row_counter ][ $cols_counter ]->setProperty('valign', $this->default_valign);
				unset($cell);
			}
		}

		$this->setNumOfRows($this->getNumOfRows() + $rows);
		return $row_pointer;
	}

	/**
	 * Returns the number of rows in the sheet.
	 * @access public
	 * @return integer	The number of rows in the sheet.
	 */
	function getNumOfRows()
	{
		return $this->rows;
	}

	/**
	 * Returns the index of last occupied row.
	 * @access public
	 * @return integer	The index of the last occupied row.
	 */
	function getLastOccupiedRow()
	{
		for ($rowcounter = $this->rows; $rowcounter >= 1; $rowcounter--) {
			if (array_key_exists('occupied', $this->rows_infos[$rowcounter]) and $this->rows_infos[$rowcounter]['occupied']) {
				return $rowcounter;
			}
		}

		return 0;
	}

	/**
	 * Sets the index of the last occupied row.
	 * This is a private method.
	 * @access private
	 * @param integer	The index of the row.
	 */
	function setLastOccupiedRow($index)
	{
		$this->last_occupied_row = $index;
	}

	/**
	 * Marks a row as occupied.
	 * @access private
	 * @param integer	The index of the row
	 */
	function setRowOccupied($index)
	{
		$this->rows_infos[$index]['occupied'] = true ;
	}

	/**
	 * Marks a row as free.
	 * @access private
	 * @param integer	The index of the row
	 */
	function setRowFree($index)
	{
		$this->rows_infos[$index]['occupied'] = false ;
	}

	/**
	 * Returns the number of columns in sheet.
	 * @access public
	 * @return integer	The number of columns in sheet.
	 */
	function getNumOfCols()
	{
		return $this->cols;
	}

	/**
	 * Sets the number of rows in the sheet.
	 * @access private
	 * @param integer	The number of rows in the sheet
	 */
	function setNumOfRows($rows)
	{
		$this->rows = $rows;
	}

	/**
	 * Sets the number of columns in the sheet.
	 * @access private
	 * @param integer	The desired row
	 */
	function setNumOfCols($cols)
	{
		$this->cols = $cols;
	}

	/**
	 * Renders the sheet in HTML string.
	 * @access public
	 * @return string	HTML rendered sheet
	 */
	function getAsString()
	{
		$id = $this->getId();
		if ($this->isVisible()) {
   			$header			= "<table class='sheet' ";
   			$close_header	= ">\n" ;
   			$footer			= "</table>" ;
   			$content		= "" ;

   			for ($row_counter = 1; $row_counter <= ($this->getNumOfRows()); $row_counter++) {
   				$content .= " <tr>\n" ;
   				for( $col_counter = 1; $col_counter <= ( $this->getNumOfCols() ); $col_counter++ ) {
    				$content .= "  " . $this->grid[$row_counter][$col_counter]->getAsString() . "\n" ;
    			}
    			$content .= " </tr>\n" ;
   			}

   			return $header . $this->composeStringProperties() . $close_header . $content . $footer ;
		} else {
			return "<div id='$id' class='hidden'></div>";
		}
	}

	/**
	 * Returns true if the grid has been defined.
	 * @access public
	 * @return boolean
	 */
	function isGridDefined()
	{
		return $this->grid_defined;
	}

	/**
	 * Sets default align for sheet cells
	 * @access public
	 */
	function setDefaultAlign($align)
	{
		$this->default_align = $align;
	}

	/**
	 * Sets default vertical align for sheet cells
	 * @access public
	 */
	function setDefaultValign($valign)
	{
		$this->default_valign = $valign;
	}
}

/**
 * The basic element of sheets: SHEET CELL.
 * Memorized informations about anchored widgetm
 * rowspan and colspan.
 * @author Fabrizio Balliano
 * @package p4a
 */
class P4A_Sheet_Cell extends P4A_Widget
{
	/**
	 * Occupational state of the cell.
	 * @access private
	 * @var boolean
	 */
	var $occupied = false;

	/**
	 * Reference to the anchored widget.
	 * @access public
	 * @var widget
	 */
	var $widget	= null;

	/**
	 * Class constructor.
	 * Inizialize the cell setting rowspan and colspan.
	 */
	function P4A_Sheet_Cell($name)
	{
		parent::P4A_Widget($name);
		$this->properties[ 'rowspan'] = 1;
		$this->properties['colspan'] = 1;
		//$this->properties['nowrap'] = 'nowrap';
		$this->properties['valign']	= 'top';
	}

	/**
	 * Anchors a widget to the cell.
	 * @param widget	The widget to anchor.
	 * @param integer	The number of rows to occupy.
	 * @param integer	The numer of columns to occupy.
	 */
	function anchor(&$widget, $rowspan = 1, $colspan = 1)
	{
		unset($this->widget);
		$this->widget =& $widget;
		$this->setOccupied();

		$this->setProperty( 'rowspan', $rowspan );
		$this->setProperty( 'colspan', $colspan );
	}

	/**
	 * Renders the cell in HTML.
	 * The cell is rendered only if visible.
	 * @return string
	 */
	function getAsString()
	{
		$header			= "<td class='sheet_cell' ";
		$close_header	= ">";
		$footer			= "</td>";

		if ($this->isOccupied()) {
			if (is_object($this->widget)) {
				$content = $this->widget->getAsString();
			} else {
				$content = $this->widget;
			}
		} else {
			$content = '&nbsp;';
		}

		if ($this->isVisible()) {
			return $header . $this->composeStringProperties() . $close_header . $content . $footer;
		} else {
			return '';
		}
	}

	/**
	 * Returns true if the cell is occupied.
	 * @return boolean
	 */
	function isOccupied()
	{
		return $this->occupied;
	}

	/**
	 * Returns true if the cell is free.
	 * @return boolean
	 */
	function isFree()
	{
		return !$this->occupied;
	}

	/**
	 * Mark the cell as occupied.
	 */
	function setOccupied()
	{
		$this->occupied = true;
	}

	/**
	 * Mark the cell as free.
	 */
	function setFree()
	{
		unset($this->widget);
		$this->occupied = false;
	}
}