<?php
class P4A_Grid extends P4A_Table
{
	public function __construct($name)
	{
		parent::__construct($name);
		$this->addCssClass('p4a_table');
		$this->useTemplate('grid');
	}
	
	public function preChange($params = null)
	{
		$params[0] = base64_decode($params[0]);
		return $this->actionHandler('onChange', $params);
	}
	
	public function autoSave()
	{
		$this->intercept($this,'onChange','saveData');
		return $this;
	}
	
	public function saveData($obj,$params)
	{
		$row[$params[1]] = $params[2];
		$this->data->saveRow($row,$params[0]);
	}
	
	public function getRows($num_page, $rows)
	{
		$p4a = P4A::singleton();

		$aReturn = array();
		$aCols = $this->getVisibleCols();
		$enabled = $this->isEnabled();

		if ($this->isActionTriggered('beforedisplay')) {
			$rows = $this->actionHandler('beforedisplay', $rows);
		}

		$pk = $this->data->getPK();
		
		$i = 0;
		$j = 0;
		$z = 0;
		
		$obj_id = $this->getID();
		
		foreach ($rows as $row) {
			
			$pk_value = $row[$pk];
			$pk_value_64 = substr(base64_encode($pk_value),0,-2);
			
			foreach($aCols as $col_name) {
				$col_enabled = $this->cols->$col_name->isEnabled();
				$aReturn[$i]['cells'][$j]['class'] = ($enabled and $col_enabled) ? 'p4a_grid_td p4a_grid_td_enabled': 'p4a_grid_td p4a_grid_td_disabled';
				$aReturn[$i]['cells'][$j]['clickable'] = ($enabled and $col_enabled) ? 'clickable' : '';
				$aReturn[$i]['cells'][$j]['id'] = $obj_id . '_' . $pk_value_64 . '_' . $z . '_' . $col_name;
				
				if ($this->cols->$col_name->isFormatted()) {
					if ($this->cols->$col_name->isActionTriggered('onformat')) {
						$aReturn[$i]['cells'][$j]['value'] = $this->cols->$col_name->actionHandler('onformat', $row[$col_name], $this->data->fields->$col_name->getType(), $this->data->fields->$col_name->getNumOfDecimals());
					} else {
						$aReturn[$i]['cells'][$j]['value'] = $p4a->i18n->format($row[$col_name], $this->data->fields->$col_name->getType(), $this->data->fields->$col_name->getNumOfDecimals(), false);
					}
				} else {
					$aReturn[$i]['cells'][$j]['value'] = $row[$col_name];
				}
				
				$aReturn[$i]['cells'][$j]['type'] = $this->data->fields->$col_name->getType();
				$j++;
				$z++;
			}
			$i++;
		}
		return $aReturn;
	}	
	
}