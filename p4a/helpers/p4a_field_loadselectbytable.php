<?php
/**
 * P4A - PHP For Applications.
 *
 * The code within this file is public domain.
 *
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */

function p4a_field_loadSelectByTable(&$field, $params)
{
	list($table, $pk, $description_field) = $params;
	if (!$description_field) {
		$description_field = $pk;
	}

	$source =& $field->build('p4a_db_source', 'source');
	$source->setTable($table);
	$source->setPK($pk);
	$source->addOrder($description_field);
	$source->load();

	$field->setType('select');
	$field->setSource($source);
	$field->setSourceDescriptionField($description_field);
}