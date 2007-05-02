<?php
function p4a_mask_settableassource($mask,$params)
{
	list($table,$pk) = $params;
	$source =& $mask->build('p4a_db_source','source');
	$source->setTable($table);
	$source->setPK($pk);
	$source->load();
	$source->firstRow();

	$mask->setSource($source);
}