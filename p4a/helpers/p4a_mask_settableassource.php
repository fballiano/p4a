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

function p4a_mask_setTableAsSource($mask, $params)
{
	list($table, $pk) = $params;
	$source =& $mask->build('p4a_db_source', 'source');
	$source->setTable($table);
	$source->setPK($pk);
	$source->load();
	$source->firstRow();

	$mask->setSource($source);
}