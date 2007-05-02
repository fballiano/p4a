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

function p4a_mask_useToolbar(&$obj, $params)
{
	$toolbar = $params[0];
	$obj->build("p4a_{$toolbar}_toolbar", 'toolbar');
	$obj->toolbar->setMask($obj);
	$obj->display('top', $obj->toolbar);
}