<?php
function p4a_field_loadSelectByTable(&$field,$params)
{
	list($table,$pk,$description_field) = $params;
	if (!$description_field) {
		$description_field = $pk;
	}

	$source =& $field->build('p4a_db_source','source');
	$source->setTable($table);
	$source->setPK($pk);
	$source->addOrder($description_field);
	$source->load();

	$field->setType('select');
	$field->setSource($source);
	$field->setSourceDescriptionField($description_field);
}
