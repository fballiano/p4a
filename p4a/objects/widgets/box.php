<?php
	class P4A_Box extends P4A_Widget
	{
		function getAsString()
		{
			if (!$this->isVisible()) {
				$string = '';
			} else {
				$properties = $this->composeStringProperties();
				$actions = $this->composeStringActions();
				$value = $this->getValue();
				$string = "<div $properties $actions>$value</div>";
			}
			return $string;
		}
	}
?>
