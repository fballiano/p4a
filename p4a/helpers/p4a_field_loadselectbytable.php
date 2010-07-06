<?php
/**
 * P4A - PHP For Applications.
 *
 * The code within this file is public domain.
 *
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a_helpers
 */

function P4A_Field_loadSelectByTable($field, $params)
{
	list($table, $pk, $description_field) = $params;
	if (!$description_field) {
		$description_field = $pk;
	}

	$field->build('p4a_db_source', 'source');
	$field->source->setTable($table);
	$field->source->setPK($pk);
	$field->source->addOrder($description_field);
	$field->source->load();

	$field->setType('select');
	$field->setSource($field->source);
	$field->setSourceDescriptionField($description_field);
}