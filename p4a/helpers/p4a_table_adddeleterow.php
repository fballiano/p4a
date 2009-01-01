<?php
/**
 * P4A - PHP For Applications.
 *
 * The code within this file is public domain.
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a_helpers
 */

function P4A_Table_addDeleteRow($table, $params)  
{      
	if (isset($params[0])) {
    	$col_label = $params[0];
    } else {
    	$col_label = 'Delete';
    }
        
    if (isset($params[1])) {
    	$message = $params[1];
    } else {
    	$message = 'Delete current element';       		    
    }

    $table->addActionCol('delete');
    $table->cols->delete->setWidth(150)
    					->setLabel($col_label)
						->requireConfirmation('onClick', $message);
	$table->data->intercept($table->cols->delete,'afterClick','deleteRow');

	return $table;
}
