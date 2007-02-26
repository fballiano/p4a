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
	 * Tabular rapresentation of a data source.
	 * This is a complex widget that's used to allow users to navigate
	 * data sources and than (for example) edit a record or view details etc...
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Table extends P4A_Widget
	{
		/**
		 * Data source associated with the table.
		 * @var data_source
		 * @access private
		 */
		var $data = null;

		/**
		 * The gui widgets to allow table navigation.
		 * @var table_navigation_bar
		 * @access private
		 */
		var $navigation_bar = null;

		/**
		 * The table toolbar.
		 * @var toolbar
		 * @access private
		 */
		var $toolbar = null;

		/**
		 * All the table's rows.
		 * @var rows
		 * @access public
		 */
		var $rows = null;

		/**
		 * Decides if the table will show the "field's header" row.
		 * @var boolean
		 * @access private
		 */
		var $_show_headers = true;

		/**
		 * Stores the table's structure (table_cols).
		 * @var array
		 * @access public
		 */
		var $cols = array();

		/**
		 * Displaying order of columns.
		 * @var array
		 * @access private
		 */
		var $_cols_order = array();

		/**
		 * A title (caption) for the table.
		 * @var string
		 * @access private
		 */
		var $_title = "";

		/**
		 * Automatically add the navigation bar?
		 * @var boolean
		 * @access private
		 */
		var $_auto_navigation_bar = true;

		/**
		 * Wich page is shown?
		 * @var integer
		 * @access private
		 */
		var $_current_page_number = 1;

		/**
		 * Class constructor.
		 * @param string				Mnemonic identifier for the object.
		 * @access private
		 */
		function P4A_Table($name)
		{
			parent::P4A_Widget($name);
			$this->useTemplate('table');
		}

		/**
		 * Sets the title for the table
		 * @param string		The title.
		 * @access public
		 * @see $title
		 */
		function setTitle($title)
		{
			$this->_title = $title;
		}

		/**
		 * Returns the title of the table.
		 * @return string
		 * @access public
		 */
		function getTitle()
		{
			return $this->_title;
		}

		/**
		 * Sets the data source that the table will navigate.
		 * @param data_source		The data source.
		 * @access public
		 */
		function setSource(&$data_source)
		{
			unset($this->data);
			$this->data =& $data_source;

			$this->setDataStructure($this->data->fields->getNames());

			$this->build("p4a_table_rows", "rows");
			$this->addNavigationBar();
		}

		/**
		 * Sets the table's structure (fields).
		 * @param array		All the fields.
		 * @access public
		 */
		function setDataStructure($array_fields)
		{
			$this->build('p4a_collection', 'cols');

			foreach($array_fields as $field) {
				$this->addCol($field);
			}
		}

		/**
		 * Adds a column to the data structure.
		 * @param string		Column name.
		 * @access public
		 */
		function addCol($column_name)
		{
			$this->cols->build("p4a_table_col",$column_name);
		}

		function addActionCol($column_name)
		{
			$this->cols->build("p4a_table_col",$column_name);
			$this->cols->$column_name->setType('action');
			$this->cols->$column_name->addAction('onClick');
			$this->cols->$column_name->setValue($this->cols->$column_name->getLabel());
		}

		/**
		 * Returns the HTML rendered object.
		 * @access public
		 */
		function getAsString()
		{
			$p4a =& P4A::singleton();

			if (!$this->isVisible()) {
				return '<div id="' . $this->getId() . '">';
			}
			$width = $this->getStyleProperty("width");
			if (substr($width,-2) == "px") {
				$width = substr($width,0,-2);
				$width = (integer)$width -20;
				$width = "{$width}px";
			} else {
				$width = "95%";
			}
			$this->addTempVar('table_width', $width);

			// if for some reason this page is empty we go back to page one
			$num_page = $this->getCurrentPageNumber();
			$rows = $this->data->page($num_page, false);
			if (empty($rows)) {
				$num_page = 1;
				$this->setCurrentPageNumber($num_page);
				$rows = $this->data->page($num_page, false);
			}

			if ($this->toolbar !== null and
				$this->toolbar->isVisible()) {
				$this->addTempVar('toolbar', $this->toolbar->getAsString());
			}

			if ($this->navigation_bar !== null) {
				if ($this->_auto_navigation_bar) {
					if ($this->data->getNumPages() > 1) {
						$this->addTempVar('navigation_bar', $this->navigation_bar->getAsString());
					}
				} else {
					if ($this->navigation_bar->isVisible()) {
						$this->addTempVar('navigation_bar', $this->navigation_bar->getAsString());
					}
				}
			}

			$visible_cols = $this->getVisibleCols();

			if($this->_show_headers) {
				$headers = array();
				$i = 0;

				$is_orderable	= false;
				$order_field	= null;
				$order_mode		= null;

				if ($this->data->isSortable()) {
					$is_orderable = true;

					if ($this->data->hasOrder()) {
    					$order = $this->data->getOrder();
    					list($order_field, $order_mode)	= each($order);
					}
				}

				foreach($visible_cols as $col_name) {
					$col =& $this->cols->$col_name;
					if ($col->getType() == 'action') {
						$headers[$i]['value']  = '&nbsp;';
						$headers[$i]['order']  = '';
						$headers[$i]['action'] = '';
					} else {
						$headers[$i]['value']		= $col->getLabel();
						$headers[$i]['order']		= null;

						if ($is_orderable and $col->isOrderable()) {
							$headers[$i]['action'] = $col->composeStringActions();
						} else {
							$headers[$i]['action'] = "";
						}

						$data_field =& $this->data->fields->{$col->getName()};
						$field_name = $data_field->getName();
						$complete_field_name = $data_field->getTable() . "." . $data_field->getName();
						if ($is_orderable and ($order_field == $field_name or $order_field == $complete_field_name)) {
							 $headers[$i]['order'] = strtolower($order_mode);
						}
					}
					$i++;
				}
				$this->addTempVar('headers', $headers);
			}

			$table_cols = array();
			foreach ($visible_cols as $col_name) {
				$col =& $this->cols->$col_name;
				$a = array();
				$a['properties'] = $col->composeStringProperties();
				$table_cols[] = $a;
			}
			$this->addTempVar('table_cols', $table_cols);

			if ($this->data->getNumRows() > 0) {
				$this->addTempVar('table_rows', $this->rows->getRows($num_page, $rows));
			} else {
				$this->addTempVar('table_rows', null);
			}
			$return = $this->fetchTemplate();
			$this->clearTempVars();
			return $return;
		}

		/**
		 * TO BE IMPLEMENTED
		 */
		function newToolbar($toolbar)
		{
			unset($this->toolbar);
			$this->build("P4A_Toolbar", "toolbar");
		}

		/**
		 * Adds a generic toolbar to the table.
		 * @access public
		 */
 		function addToolbar(&$toolbar)
		{
			unset($this->toolbar);
			$this->toolbar =& $toolbar;
		}

		/**
		 * Makes the toolbar visible.
		 * @access public
		 */
		function showToolbar()
		{
			if (is_object($this->toolbar)) {
				$this->toolbar->setVisible();
			} else {
				P4A_Error('NO TOOLBAR');
			}
		}

		/**
		 * Makes the toolbar invisible.
		 * @access public
		 */
		function hideToolbar()
		{
			if (is_object($this->toolbar)) {
				$this->toolbar->setInvisible();
			} else {
				P4A_Error('NO TOOLBAR');
			}
		}

		/**
		 * Adds the navigation bar to the table.
		 * @access public
		 */
		function addNavigationBar()
		{
			$this->build("p4a_table_navigation_bar", "navigation_bar");
		}

		/**
		 * Makes the navigation bar visible.
		 * @access public
		 */
		function showNavigationBar()
		{
			if ($this->navigation_bar === NULL) {
				$this->addNavigationBar();
			}
			$this->navigation_bar->setVisible();
			$this->_auto_navigation_bar = FALSE;
		}

		/**
		 * Makes the navigation bar hidden.
		 * @access public
		 */
		function hideNavigationBar()
		{
			if ($this->navigation_bar !== NULL) {
				$this->navigation_bar->setInvisible();
			}
			$this->_auto_navigation_bar = FALSE;
		}

		/**
		 * Sets the title bar visible
		 * @access public
		 */
		function showTitleBar()
		{
			if ($this->title_bar !== NULL) {
				$this->setTitle($this->getName());
			}
			$this->title_bar->setVisible();
		}

		/**
		 * Sets the title bar hidden
		 * @access public
		 */
		function showHeaders()
		{
			$this->_show_headers = TRUE;
		}

		/**
		 * Sets the header row hidden
		 * @access public
		 * @see $_show_headers
		 */
		function hideHeaders()
		{
			$this->_show_headers = FALSE;
		}

		/**
		 * Return an array with all columns id.
		 * @access public
		 * @return array
		 */
		function getCols()
		{
			return $this->cols->getNames();
		}

		/**
		 * Return an array with all id of visible columns.
		 * @access public
		 * @return array
		 */
		function getVisibleCols()
		{
			$return = array();

			if (!empty($this->_cols_order)) {
				foreach ($this->_cols_order as $col) {
					if ($this->cols->$col->isVisible()) {
						$return[] = $col;
					}
				}
			} else {
				while ($col =& $this->cols->nextItem()) {
					if ($col->isVisible()) {
						$return[] = $col->getName();
					}
				}
			}

			return $return;
		}

		/**
		 * Return an array with all id of invisible columns.
		 * @access public
		 * @return array
		 */
		function getInvisibleCols()
		{
			$return = array();

			while ($col =& $this->cols->nextItem()) {
				if (!$col->isVisible()) {
					$return[] = $col->getName();
				}
			}

			return $return;
		}

		/**
		 * Sets all passed columns visible.
		 * If no array is given, than sets all columns visible.
		 * @access public
		 * @params array	Columns id in indexed array.
		 */
		function setVisibleCols($cols = array())
		{
			$this->setInvisibleCols();
			if (sizeof($cols) == 0) {
				$cols = $this->getCols();
			}

			foreach ($cols as $col) {
				if (isset($this->cols->$col)) {
					$this->cols->$col->setVisible();
				} else {
					P4A_Error("Unknow column $col");
				}
			}

			$this->_cols_order = $cols;
		}

		/**
		 * Sets all passed columns invisible.
		 * If no array is given, than sets all columns invisible.
		 * @access public
		 * @params array	Columns id in indexed array.
		 */
		function setInvisibleCols($cols = array())
		{
			if (sizeof( $cols ) == 0) {
				$cols = $this->getCols();
			}

			foreach ($cols as $col) {
				if (isset($this->cols->$col)) {
					$this->cols->$col->setVisible(false);
				} else {
					P4A_Error("Unknow column $col");
				}
			}
		}

		/**
		 * Returns the current page number
		 * @access public
		 * @return integer
		 */
		function getCurrentPageNumber()
		{
			return $this->_current_page_number;
		}

		/**
		 * Sets the current page number
		 * @access public
		 * @params integer
		 */
		function setCurrentPageNumber($page)
		{
			$this->_current_page_number = $page;
		}

		/**
		 * Sets the page number reading it from the data source
		 * @access public
		 */
		function syncPageWithSource()
		{
			$this->setCurrentPageNumber($this->data->getNumPage());
		}
	}

	/**
	 * Keeps the data for a single table column.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Table_Col extends P4A_Widget
	{
		/**
		 * Keeps the header string.
		 * @var string
		 * @access private
		 */
		var $header = NULL;

		/**
		 * Data source for the field.
		 * @var data_source
		 * @access private
		 */
		var $data = NULL;

		/**
		 * The data source member that contains the values for this field.
		 * @var string
		 * @access private
		 */
		var $data_value_field = NULL ;

		/**
		 * The data source member that contains the descriptions for this field.
		 * @var string
		 * @access private
		 */
		var $data_description_field	= NULL ;

		/**
		 * Tells if the fields content is formatted or not.
		 * @var boolean
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
		 * Tells if the table can order by this col
		 * @var boolean
		 * @access private
		 */
		var $orderable = true;

		/**
		 * Type of the column
		 * @access private
		 * @var string
		 */
		var $_type = "text";

		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function P4A_Table_Col($name)
		{
			parent::P4A_Widget($name);
			$this->setDefaultLabel();
			$this->addAjaxAction('onClick');
		}

		/**
		 * Sets the header for the column.
		 * @param string		The header
		 * @access public
		 * @see $header
		 */
		function setHeader($header)
		{
			$this->setLabel($header);
		}

		/**
		 * Returns the header for the column.
		 * @access public
		 * @see $header
		 */
		function getHeader()
		{
			return $this->getLabel();
		}

		/**
		 * If we use fields like combo box we have to set a data source.
		 * By default we'll take the data source primary key as value field
		 * and the first fiels (not pk) as description.
		 * @param data_source		The data source.
		 * @access public
		 */
		function setSource(&$data_source)
		{
			unset( $this->data ) ;
			$this->data =& $data_source;
			$pk = $this->data->getPk();

			if ($pk !== NULL) {
				if( $this->getSourceValueField() === NULL ) {
					if (is_array($pk)) {
						P4A_Error("FEATURE NOT IMPLEMENTED: Multiple pk on table col.");
					} else {
						$this->setSourceValueField($pk) ;
					}
				}

				if ($this->getSourceDescriptionField() === NULL) {
					$source_value = $this->getSourceValueField();
					$names = $this->data->fields->getNames();
					foreach ($names as $name) {
						if ($name != $source_value) {
							$this->setSourceDescriptionField($name) ;
							break;
						}
					}
				}
			}
		}

		/**
		 * Sets what data source member is the keeper of the field's value.
		 * @param string		The name of the data source member.
		 * @access public
		 */
		function setSourceValueField( $name )
		{
			// No controls if $name exists...
			// too many controls may be too performance expensive.
			$this->data_value_field = $name ;
		}

		/**
		 * Sets what data source member is the keeper of the field's description.
		 * @param string		The name of the data source member.
		 * @access public
		 */
		function setSourceDescriptionField( $name )
		{
			// No controls if $name exists...
			// too many controls may be too performance expensive
			$this->data_description_field = $name ;
		}

		/**
		 * Returns the name of the data source member that keeps the field's value.
		 * @return string
		 * @access public
		 */
		function getSourceValueField()
		{
			return $this->data_value_field ;
		}

		/**
		 * Returns the name of the data source member that keeps the field's description.
		 * @return string
		 * @access public
		 */
		function getSourceDescriptionField()
		{
			return $this->data_description_field ;
		}

		/**
		 * Translate the value with the description
		 * @param string		The value to translate
		 * @return string
		 * @access public
		 */
		function getDescription($value)
		{
			if (!$this->data) {
				return $value;
			} else {
				$row = $this->data->getPkRow($value);
				if (is_array($row)){
					return $row[$this->data_description_field];
				}else{
					return $value;
				}
			}
		}

		/**
		 * Returns true if a formatting format for the field has been set.
		 * @access public
		 * @return boolean
		 */
		function isFormatted()
		{
			return $this->formatted;
		}

		/**
		 * Sets the column as formatted.
		 * @access public
		 */
		function setFormatted($value = true)
		{
			$this->formatted = $value;
		}

		/**
		 * Sets the column as not formatted.
		 * @access public
		 */
		function unsetFormatted()
		{
			$this->formatted = false;
		}

		/**
		 * Sets the formatter and format for the column.
		 * This also turns formatting on.<br>
		 * Eg: set_format('numbers', 'decimal')
		 * @access public
		 * @param string	The formatter name.
		 * @param string	The format name.
		 */
		function setFormat($formatter_name, $format_name)
		{
			$this->formatter_name = $formatter_name;
			$this->format_name = $format_name;
			$this->setFormatted();
		}

		/**
		 * Removes formatting options and turns formatting off.
		 * @access public
		 */
		function unsetFormat()
		{
			$this->formatter_name = NULL;
			$this->format_name = NULL;
			$this->unsetFormatted();
		}

		/**
		 * Sets the ability to order by this column
		 * @access public
		 */
		function setOrderable($orderable = true)
		{
			$this->orderable = $orderable;
		}

		/**
		 * Tell if the column is orderable or not
		 * @access public
		 */
		function isOrderable()
		{
			return $this->orderable;
		}

		/**
		 * Sets the type of the column (text|image)
		 * @access public
		 * @param string
		 */
		function setType($type)
		{
			$this->_type = $type;
		}

		/**
		 * Gets the type of the column (text|image)
		 * @access public
		 * @return string
		 */
		function getType()
		{
			return $this->_type;
		}

		function onClick($aParams)
		{
			if ($this->isActionTriggered('beforeClick')) {
				if ($this->actionHandler('beforeClick', $aParams) == ABORT) return ABORT;
			}

			if ($this->getType() == 'action') {
				$p4a =& P4A::singleton();
				$cols =& $p4a->getObject($this->getParentID());
				$table =& $p4a->getObject($cols->getParentID());
				if ($table->data->row($aParams[0] + (($table->getCurrentPageNumber() - 1) * $table->data->getPageLimit()) + 1) == ABORT) return ABORT;
			} else {
				$this->order();
			}

			if ($this->isActionTriggered('afterClick')) {
				if ($this->actionHandler('afterClick', $aParams) == ABORT) return ABORT;
			}
		}

		function order()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent =& $p4a->getObject($parent->getParentID());
			$parent->redesign();

			if ($parent->data->isSortable()) {
				$data_field =& $parent->data->fields->{$this->getName()};
				$field_name = $data_field->getName();
				$complete_field_name = $data_field->getTable() . "." . $data_field->getName();

				$new_order = P4A_ORDER_ASCENDING;
				$order_mode = P4A_ORDER_ASCENDING;
				if ($parent->data->hasOrder()) {
					$order = $parent->data->getOrder();
					list($order_field, $order_mode)	= each($order);

					if ($order_field == $complete_field_name or $order_field == $field_name) {
    					if ($order_mode == P4A_ORDER_ASCENDING) {
    						$order_mode = P4A_ORDER_DESCENDING;
    					} else {
							$order_mode = P4A_ORDER_ASCENDING;
						}
					} else {
						$order_mode = P4A_ORDER_ASCENDING;
					}
				}
				if ($data_field->getAliasOf()){
					$order_field = $data_field->getName();
				} else {
					$order_field = $data_field->getTable() . "." . $data_field->getName();
				}
				$parent->data->setOrder($order_field, $order_mode);
				$parent->data->updateRowPosition();
			}
		}
	}

	/**
	 * Keeps all the data for all the rows.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Table_Rows extends P4A_Widget
	{
		/**
		 * Class constructor.
		 * By default we add an onClick action.
		 * @param string		Mnemonic identifier for the object
		 * @access private
		 */
		function P4A_Table_Rows($name = 'rows')
		{
			parent::P4A_Widget($name);
			$this->addAction('onClick');
		}

		/**
		 * Sets the max height for the data rows.
		 * This is done adding a scrollbar to the table body.
		 * @param integer		The desidered height.
		 * @param string		Measure unit
		 * @access public
		 */
		function setMaxHeight($height, $unit = 'px')
		{
			$this->setStyleProperty('max-height', $height . $unit);
		}

		/**
		 * Retrive data for the current page.
		 * @return array
		 * @access private
		 */
		function getRows($num_page, $rows)
		{
			$p4a =& P4A::singleton();

			$aReturn = array();
			$parent =& $p4a->getObject($this->getParentID());
			$num_page_from_data_source = $parent->data->getNumPage();
			$aCols = $parent->getVisibleCols();
			$limit = $parent->data->getPageLimit();
			$offset = $parent->data->getOffset();
			$enabled = $this->isEnabled();
			$action = null;

			if ($this->isActionTriggered('beforeDisplay')) {
				$rows = $this->actionHandler('beforeDisplay', $rows);
			}

			$i = 0;
			foreach ($rows as $row_number=>$row) {
				$j = 0;
				if ($i%2 == 0) {
					$aReturn[$i]['row']['even'] = true;
				} else {
					$aReturn[$i]['row']['even'] = false;
				}

				if (($num_page == $num_page_from_data_source) and ($row_number + $offset + 1 == $parent->data->getRowNumber())) {
					$aReturn[$i]['row']['active'] = true;
				} else {
					$aReturn[$i]['row']['active'] = false;
				}

				if (isset($row['_p4a_enabled'])) {
					$row_enabled = $row['_p4a_enabled'];
				} else {
					$row_enabled = true;
				}

				foreach($aCols as $col_name) {
					$aReturn[$i]['cells'][$j]['action'] = ($enabled and $row_enabled) ? $this->composeStringActions(array($row_number, $col_name)) : '';
					$aReturn[$i]['cells'][$j]['clickable'] = ($enabled and $row_enabled) ? 'clickable' : '';

					if ($parent->cols->$col_name->data) {
						$aReturn[$i]['cells'][$j]['value'] = $parent->cols->$col_name->getDescription($row[$col_name]);
						$aReturn[$i]['cells'][$j]['type'] = $parent->data->fields->$col_name->getType();
					} elseif ($parent->cols->$col_name->getType() == "image") {
						$value = $row[$col_name];
						if (!empty($value)) {
							$value = substr($value, 1, -1);
							$value = explode(',', $value);
							$image_src = P4A_UPLOADS_PATH . "/{$value[1]}";
							if (P4A_GD) {
								$image_src = P4A_ROOT_PATH . "/p4a/libraries/phpthumb/phpThumb.php?src=$image_src&amp;h=40";
								$aReturn[$i]['cells'][$j]['value'] = "<img src='$image_src' height='40' alt='' />";
							} else {
								$aReturn[$i]['cells'][$j]['value'] = "<img src='$image_src' height='40' alt='' />";
							}
						}
						$aReturn[$i]['cells'][$j]['type'] = $parent->data->fields->$col_name->getType();
					} elseif ($parent->cols->$col_name->getType() == "action") {
						$aReturn[$i]['cells'][$j]['value'] = $parent->cols->$col_name->getValue();
						$aReturn[$i]['cells'][$j]['type'] = 'action';
						$aReturn[$i]['cells'][$j]['action'] = $enabled ? $parent->cols->$col_name->composeStringActions(array($row_number, $col_name)) : '';
					} elseif ($parent->cols->$col_name->isFormatted()) {
						if (($parent->cols->$col_name->formatter_name === null) and ($parent->cols->$col_name->format_name === null)) {
							$aReturn[$i]['cells'][$j]['value'] = str_replace(' ', '&nbsp;', $p4a->i18n->autoFormat($row[$col_name], $parent->data->fields->$col_name->getType()));
						} else {
							$aReturn[$i]['cells'][$j]['value'] = str_replace(' ', '&nbsp;', $p4a->i18n->{$parent->cols->$col_name->formatter_name}->format( $row[$col_name], $p4a->i18n->{$parent->cols->$col_name->formatter_name}->getFormat($parent->cols->$col_name->format_name)));
						}
						$aReturn[$i]['cells'][$j]['type'] = $parent->data->fields->$col_name->getType();
					} else {
						$aReturn[$i]['cells'][$j]['value'] = $row[$col_name];
						$aReturn[$i]['cells'][$j]['type'] = $parent->data->fields->$col_name->getType();
					}

					$j++;
				}
				$i++;
			}

			return $aReturn;
		}

		/**
		 * onClick action handler for the row.
		 * We move pointer to the clicked row.
		 * @param array		All passed params.
		 * @access public
		 */
		function onClick($aParams)
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());

			if ($this->actionHandler('beforeClick', $aParams) == ABORT) return ABORT;

			if ($parent->data->row($aParams[0] + (($parent->getCurrentPageNumber() - 1) * $parent->data->getPageLimit()) + 1) == ABORT) return ABORT;

			if ($this->actionHandler('afterClick', $aParams) == ABORT) return ABORT;
		}
	}

	/**
	 * The gui widgets to navigate the table.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Table_Navigation_Bar extends P4A_Frame
	{
		/**
		 * Buttons collection
		 * @var p4a_collection
		 * @access public
		 */
		var $buttons = null;

		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function P4A_Table_Navigation_Bar()
		{
			$p4a =& P4A::singleton();

			parent::P4A_Frame("table_navigation_bar");
			$this->build("p4a_collection", "buttons");
			$this->setStyleProperty("float", "none");

			$this->addButton('button_go', 'apply', 'right');
			$this->intercept($this->buttons->button_go, 'onClick', 'goOnClick');

			$field_num_page =& $this->buttons->build(P4A_FIELD_CLASS, 'field_num_page');
			$field_num_page->label->setStyleProperty("text-align", "right");
			$field_num_page->label->setWidth(80);
			$this->buttons->field_num_page->setWidth(30);
			$this->buttons->field_num_page->addAjaxAction('onReturnPress');
			$this->intercept($this->buttons->field_num_page, 'onReturnPress', 'goOnClick');
			$this->anchorRight($field_num_page);

			$current_page =& $this->buttons->build('p4a_label', 'current_page');
			$this->anchorLeft($current_page);

			if ($p4a->isHandheld()) {
				$this->addButton('button_first');
				$this->buttons->button_first->setLabel('<<');
				$this->addButton('button_prev');
				$this->buttons->button_prev->setLabel('<');
				$this->addButton('button_next');
				$this->buttons->button_next->setLabel('>');
				$this->addButton('button_last');
				$this->buttons->button_last->setLabel('>>');

				$this->buttons->button_go->setVisible(false);
				$this->buttons->field_num_page->setVisible(false);
			} else {
				$this->addButton('button_last', 'last', 'right');
				$this->buttons->button_last->setValue("last_page");
				$this->addButton('button_next', 'next', 'right');
				$this->buttons->button_next->setValue("next_page");
				$this->addButton('button_prev', 'prev', 'right');
				$this->buttons->button_prev->setValue("prev_page");
				$this->addButton('button_first', 'first', 'right');
				$this->buttons->button_first->setValue("first_page");
			}

			$this->intercept($this->buttons->button_last, 'onClick', 'lastOnClick');
			$this->intercept($this->buttons->button_next, 'onClick', 'nextOnClick');
			$this->intercept($this->buttons->button_prev, 'onClick', 'prevOnClick');
			$this->intercept($this->buttons->button_first, 'onClick', 'firstOnClick');
		}

		function addButton($button_name, $icon = null, $float = "left")
		{
			$button =& $this->buttons->build("p4a_button", $button_name);
			$button->addAjaxAction('onClick');

			if (strlen($icon)>0) {
				$button->setIcon($icon);
				$button->setSize(16);
			}

			$anchor = "anchor" . $float;
			$this->$anchor($button, "2px");
			return $button;
		}

		function getAsString()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());

			$this->buttons->field_num_page->setLabel( $p4a->i18n->messages->get('go_to_page'));
			$this->buttons->field_num_page->setNewValue($parent->getCurrentPageNumber());

			$num_pages = $parent->data->getNumPages();
			if ($num_pages == 0) {
				$num_pages = 1;
			}

			$current_page  = $p4a->i18n->messages->get('current_page');
			$current_page .= ' ';
			$current_page .= $parent->getCurrentPageNumber();
			$current_page .= ' ';
			$current_page .= $p4a->i18n->messages->get('of_pages');
			$current_page .= ' ';
			$current_page .= $num_pages;
			$current_page .= ' ';
			$this->buttons->current_page->setValue($current_page);
			return parent::getAsString();
		}

		/**
		 * Action handler for "next" button click.
		 * @access public
		 */
		function nextOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());

			$num_page = $parent->getCurrentPageNumber() + 1;
			$num_pages = $parent->data->getNumPages();

			if ($num_page > $num_pages) {
				$num_page -= 1;
			}

			$parent->setCurrentPageNumber($num_page);
			$parent->redesign();
		}

		/**
		 * Action handler for "previous" button click.
		 * @access public
		 */
		function prevOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());

			$num_page = $parent->getCurrentPageNumber() - 1;
			if ($num_page < 1) {
				$num_page = 1;
			}

			$parent->setCurrentPageNumber($num_page);
			$parent->redesign();
		}

		/**
		 * Action handler for "first" button click.
		 * @access public
		 */
		function firstOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent->setCurrentPageNumber(1);
			$parent->redesign();
		}

		/**
		 * Action handler for "last" button click.
		 * @access public
		 */
		function lastOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent->setCurrentPageNumber($parent->data->getNumPages());
			$parent->redesign();
		}

		/**
		 * Action handler for "go" button click.
		 * @access public
		 */
		function goOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());

			$num_page = (int)$this->buttons->field_num_page->getNewValue();
			$num_pages = $parent->data->getNumPages();

			if ($num_page < 1) {
				$num_page = 1;
			} elseif ($num_page > $num_pages) {
				$num_page = $num_pages;
			}

			$parent->setCurrentPageNumber($num_page);
			$parent->redesign();
		}

	}