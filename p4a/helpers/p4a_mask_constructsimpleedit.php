<?php
/**
 * P4A - PHP For Applications.
 *
 * The code within this file is public domain.
 *
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a_helpers
 */

function P4A_Mask_constructSimpleEdit($mask, $params)
{
	list($source) = $params;
	
	// source
	if (is_string($source)) {
		$mask->build('P4A_DB_Source', 'source')
			->setTable($source)
			->load();
	} elseif (is_a($source, 'P4A_DB_Source')) {
		$table = $source->getTable();
		if (strlen($table) == 0) {
			trigger_error("The passed P4A_DB_Source has no master table", E_USER_ERROR);
		}
		$mask->source = $source;
	} else {
		trigger_error("You did not pass a valid \"source\" param, please pass a string or a P4A_DB_Source", E_USER_ERROR);
	}
	$mask->setSource($mask->source);
	
	// toolbar
	$mask->build('P4A_Full_Toolbar', 'toolbar')
		->setMask($mask);
	
	// table
	$mask->build('P4A_Table', 'table')
		->setSource($mask->source)
		->setWidth(500)
		->showNavigationBar();
	
	// fieldset with anchored objects
	$mask->build('P4A_Fieldset', 'fieldset');
	while ($field = $mask->fields->nextItem()) {
		$mask->fieldset->anchor($field);
	}
	
	// main frame
	$mask->frame
		->anchor($mask->table)
		->anchor($mask->fieldset);

	// last things
	$mask
		->display("top", $mask->toolbar)
		->setFocus($mask->fields->nextItem())
		->firstRow();
	
	// resetting fields collection pointer
	$mask->fields->reset();
}