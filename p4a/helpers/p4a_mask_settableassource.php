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

function P4A_Mask_setTableAsSource($mask, $params)
{
	list($table, $pk) = $params;
	$mask->build('P4A_DB_Source', 'source');
	$mask->source->setTable($table);
	$mask->source->setPK($pk);
	$mask->source->load();
	$mask->source->firstRow();

	$mask->setSource($mask->source);
}