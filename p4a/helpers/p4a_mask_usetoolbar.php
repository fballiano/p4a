<?php

function p4a_mask_useToolbar(&$obj,$params)
{
	$toolbar = $params[0];
	$obj->build("p4a_{$toolbar}_toolbar",'toolbar');
	$obj->toolbar->setMask($obj);
	$obj->display('top',$obj->toolbar);
}