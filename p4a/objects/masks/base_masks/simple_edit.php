<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/agpl.html>.
 * 
 * To contact the authors write to:                                     <br />
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

/**
 * A quick way to edit a database table.
 * This mask automatically creates a P4A_DB_Source (if you don't pass your one)
 * than creates a P4A_Table, a P4A_Full_Toolbar, a P4A_Fieldset (with all fields
 * anchored inside itself) and sets the focus on the first available field.<br />
 * The table will have a 500px default width.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Simple_Edit_Mask extends P4A_Base_Mask
{
	/**
	 * @var P4A_DB_Source
	 */
	public $source = null;
	
	/**
	 * @var P4A_Full_Toolbar
	 */
	public $toolbar = null;
	
	/**
	 * @var P4A_Table
	 */
	public $table = null;
	
	/**
	 * P4A_Fieldset
	 */
	public $fieldset = null;
	
	/**
	 * @param string|P4A_DB_Source $source Table name or P4A_DB_Source object
	 */
	public function __construct($source)
	{
		parent::__construct();
		
		// source
		if (is_string($source)) {
			$this->build('P4A_DB_Source', 'source')
				->setTable($source)
				->load();
		} elseif (is_a($source, 'P4A_DB_Source')) {
			$table = $source->getTable();
			if (strlen($table) == 0) {
				trigger_error("The passed P4A_DB_Source has no master table", E_USER_ERROR);
			}
			$this->source = $source;
		} else {
			$class = get_class($source);
			trigger_error("$class is not a valid param, please pass a string or a P4A_DB_Source", E_USER_ERROR);
		}
		$this->setSource($this->source);
		
		// toolbar
		$this->build('P4A_Full_Toolbar', 'toolbar')
			->setMask($this);
		
		// table
		$this->build('P4A_Table', 'table')
			->setSource($this->source)
			->setWidth(500)
			->showNavigationBar();
		
		// fieldset with anchored objects
		$this->build('P4A_Fieldset', 'fieldset');
		while ($field = $this->fields->nextItem()) {
			$this->fieldset->anchor($field);
		}
		
		// main frame
		$this->frame
			->anchor($this->table)
			->anchor($this->fieldset);
		
		// last things
		$this
			->display("top", $this->toolbar)
			->setFocus($this->fields->nextItem())
			->firstRow();
		
		// resetting fields collection pointer
		$this->fields->reset();
	}
}