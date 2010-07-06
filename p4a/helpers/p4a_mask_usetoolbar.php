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

function P4A_Mask_useToolbar($obj, $params)
{
	$toolbar = $params[0];
	$obj->build("P4A_{$toolbar}_Toolbar", 'toolbar');
	$obj->toolbar->setMask($obj);
	$obj->display('top', $obj->toolbar);
}