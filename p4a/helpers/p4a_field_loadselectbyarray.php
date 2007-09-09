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

function p4a_field_loadSelectByArray(&$field, $params)
{
	list($array) = $params;

	$source =& $field->build('p4a_array_source', 'source');
	$source->load($array);
	
	$field->setType('select');
	$field->setSource($source);
}