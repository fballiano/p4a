<?php

define('CALENDAR_ENGINE', 'PearDate');
require_once 'Calendar/Minute.php';

class P4A_Calendar extends P4A_Widget
{
	var $_source = null;
	var $_source_date_field = 'date';
	var $_source_time_field = 'time';
	var $_source_description_field = 'description';
	
	var $_start_date = null;
	var $toolbar = null;
	var $_type = 'month';
	var $_week_start = 0;
	var $day_start = 8;
	var $day_end = 20;
	
	function P4A_Calendar($name)
	{
		parent::P4A_Widget($name, 'cal');
		$this->addAction('onClick');
		
		$this->setStartDate(date('Y-m-01'));
		$this->setWidth(700);
		$this->setHeight(500);
		
		$this->build('p4a_toolbar', 'toolbar');
		$this->toolbar->addButton('prev', 'prev', 'left');
		$this->toolbar->buttons->prev->setSize(16);
		$this->intercept($this->toolbar->buttons->prev, 'onClick', 'prevOnClick');
		
		$this->toolbar->addButton('next', 'next', 'right');
		$this->toolbar->buttons->next->setSize(16);
		$this->intercept($this->toolbar->buttons->next, 'onClick', 'nextOnClick');
		
		$this->toolbar->addLabel('date_description', '', 'none');
		$this->toolbar->buttons->date_description->setProperty('class', 'clickable');
		$this->toolbar->buttons->date_description->addAction('onClick');
		$this->intercept($this->toolbar->buttons->date_description, 'onClick', 'titleOnClick');
	}
	
	function setSource(&$source)
	{
		$this->_source = $source;
	}
	
	function setSourceDateField($name)
	{
		$this->_source_date_field = $name;
	}
	
	function setSourceTimeField($name)
	{
		$this->_source_time_field = $name;
	}
	
	function setSourceDescriptionField($name)
	{
		$this->_source_description_field = $name;
	}
	
	function setStartDate($date=null)
	{
		if (empty($date)) {
			$date = date('Y-m-d');
		}
		
		$this->_start_date = $date;
	}
	
	function getStartDate()
	{
		return $this->_start_date;
	}
	
	function setType($type)
	{
		$this->_type = $type;
	}
	
	function getType()
	{
		return $this->_type;
	}
	
	function setWeekStart($day)
	{
		$this->_week_start = $day;
	}
	
	function getWeekStart()
	{
		return $this->_week_start;
	}
	
	function titleOnClick()
	{
		$date = substr($this->getStartDate(), 0, -2);
		$this->setStartDate("{$date}01");
		$this->setType('month');
	}
	
	function onClick($params)
	{
		$this->setType($params[0]);
		$this->setStartDate($params[1]);
	}
	
	function prevOnClick()
	{
		define('DATE_CALC_BEGIN_WEEKDAY', $this->getWeekStart());
		require_once 'Date/Calc.php';
		$date = explode('-', $this->getStartDate());
		
		switch ($this->getType()) {
			case 'month':
				$start_date = Date_Calc::beginOfPrevMonth($date[2], $date[1], $date[0], P4A_DATE);
				break;
			case 'week':
				$start_date = Date_Calc::beginOfPrevWeek($date[2], $date[1], $date[0], P4A_DATE);
				break;
			case 'day':
				$start_date = Date_Calc::prevDay($date[2], $date[1], $date[0], P4A_DATE);
				break;
		}
		
		$this->setStartDate($start_date);
	}
	
	function nextOnClick()
	{
		define('DATE_CALC_BEGIN_WEEKDAY', $this->getWeekStart());
		require_once 'Date/Calc.php';
		$date = explode('-', $this->getStartDate());
		
		switch ($this->getType()) {
			case 'month':
				$start_date = Date_Calc::beginOfNextMonth($date[2], $date[1], $date[0], P4A_DATE);
				break;
			case 'week':
				$start_date = Date_Calc::beginOfNextWeek($date[2], $date[1], $date[0], P4A_DATE);
				break;
			case 'day':
				$start_date = Date_Calc::nextDay($date[2], $date[1], $date[0], P4A_DATE);
				break;
		}
		
		$this->setStartDate($start_date);
	}
	
	function getAsMonth($events)
	{
		$p4a =& p4a::singleton();
		$this->useTemplate('calendar/month');
		require_once 'Calendar/Month/Weekdays.php';
		
		$start_date = $this->getStartDate();
		$start_year = substr($start_date, 0, 4);
		$start_month = substr($start_date, 5, 2);
		
		$month = new Calendar_Month_Weekdays($start_year, $start_month, $this->getWeekStart());
		$month->build();
		$engine = $month->getEngine();
		
		$weeks = array();
		$weeks_counter = 0;
		while ($day = $month->fetch()) {
		    if ($day->isEmpty()) {
		        $weeks[$weeks_counter]['days'][] = '';
		    } else {
				$day_iso = $day->thisYear() . '-' . str_pad($day->thisMonth(), 2, 0, STR_PAD_LEFT) . '-' . str_pad($day->thisDay(), 2, 0, STR_PAD_LEFT);
				
				if (!isset($weeks[$weeks_counter]['week_number'])) {
					$weeks[$weeks_counter]['week_number'] = $engine->getWeekNInYear($day->thisYear(), $day->thisMonth(), $day->thisDay());
					$weeks[$weeks_counter]['week_actions'] = $this->composeStringActions(array('week', $day_iso));
				}
				
				$day_number = $day->thisDay();
				$cell = array();
				$cell['day_number'] = $day_number;
				$cell['day_actions'] = $this->composeStringActions(array('day', $day_iso));
				$cell['events'] = array();
				
				foreach ($events as $event) {
					if ($event[$this->_source_date_field] == $day_iso) {
						$event_time = $p4a->i18n->autoFormat($event[$this->_source_time_field], 'time');
						$event_description = $event[$this->_source_description_field];
						$cell['events'][] = array($event_time, $event_description);
					}
				}
				
				$weeks[$weeks_counter]['days'][] = $cell;
		    }
		
		    if ($day->isLast()) {
		        $weeks_counter++;
		    }
		}
		$this->addTempVar('weeks', $weeks);
		$this->addTempVar('toolbar', $this->toolbar->getAsString());
		return $this->fetchTemplate();
	}
	
	function getAsWeek($events)
	{
		$p4a =& p4a::singleton();
		$this->useTemplate('calendar/week');
		require_once 'Calendar/Week.php';
		
		$start_date = $this->getStartDate();
		$start_year = substr($start_date, 0, 4);
		$start_month = substr($start_date, 5, 2);
		$start_day = substr($start_date, 8, 2);
		$week = new Calendar_Week($start_year, $start_month, $start_day, $this->getWeekStart());
		$week->build();
		
		$days = array();
		while ($day = $week->fetch()) {
			$day_iso = $day->thisYear() . '-' . str_pad($day->thisMonth(), 2, 0, STR_PAD_LEFT) . '-' . str_pad($day->thisDay(), 2, 0, STR_PAD_LEFT);

			$tmp = array();
			$tmp['day_actions'] = $this->composeStringActions(array('day', $day_iso));
			$tmp['day_number'] = $day->thisDay();
			$tmp['hours'] = array();
			
			$day = new Calendar_Day($day->thisYear(), $day->thisMonth(), $day->thisDay());
			$day->build();
			
			while ($hour = $day->fetch()) {
				if ($hour->thisHour()>= $this->day_start and $hour->thisHour()<= $this->day_end) {
					$tmp2 = array();
					$tmp2['time'] = $p4a->i18n->autoFormat($hour->thisHour(), 'time');
					$tmp2['events'] = array();
					
					foreach ($events as $event) {
						if ($event[$this->_source_date_field] == $day_iso and substr($event[$this->_source_time_field], 0, 2) == $hour->thisHour()) {
							$event_time = $p4a->i18n->autoFormat($event[$this->_source_time_field], 'time');
							$event_description = $event[$this->_source_description_field];
							$tmp2['events'][] = array($event_time, $event_description);
						}
					}
					
					$tmp['hours'][] = $tmp2;
				}
			}
			
			$days[] = $tmp;
		}
		
		$this->addTempVar('days', $days);
		$this->addTempVar('toolbar', $this->toolbar->getAsString());
		return $this->fetchTemplate();
	}
	
	function getAsDay($events)
	{
		$p4a =& p4a::singleton();
		$this->useTemplate('calendar/day');
		require_once 'Calendar/Day.php';
		
		$start_date = $this->getStartDate();
		$start_year = substr($start_date, 0, 4);
		$start_month = substr($start_date, 5, 2);
		$start_day = substr($start_date, 8, 2);
		$day_iso = $start_date;
		$day = new Calendar_Day($start_year, $start_month, $start_day);
		$day->build();
		$engine = $day->getEngine();
		
		$hours = array();
		while ($hour = $day->fetch()) {
			if ($hour->thisHour()>= $this->day_start and $hour->thisHour()<= $this->day_end) {
				$tmp = array();
				$tmp['time'] = $p4a->i18n->autoFormat($hour->thisHour(), 'time');
				$tmp['events'] = array();
				
				foreach ($events as $event) {
					if ($event[$this->_source_date_field] == $day_iso and substr($event[$this->_source_time_field], 0, 2) == $hour->thisHour()) {
						$event_time = $p4a->i18n->autoFormat($event[$this->_source_time_field], 'time');
						$event_description = $event[$this->_source_description_field];
						$tmp['events'][] = array($event_time, $event_description);
					}
				}
				
				$hours[] = $tmp;
			}
		}
		
		$this->addTempVar('dayname', $p4a->i18n->messages->get('days', (int)$engine->getdayofweek($start_year, $start_month, $start_day)));
		$this->addTempVar('hours', $hours);
		$this->addTempVar('toolbar', $this->toolbar->getAsString());
		return $this->fetchTemplate();
	}
	
	function getAsString()
	{
		if (!$this->isVisible()) {
			return '';
		}
		
		$p4a =& p4a::singleton();
		$p4a->active_mask->addTempCSS(P4A_THEME_PATH . '/widgets/calendar/screen.css', 'screen');
		$this->addTempVar('properties', $this->composeStringProperties());
		$this->addTempVar('style', $this->composeStringStyle());
		
		$daynames = $p4a->i18n->messages->get('days');
		$daynames = array_merge(array_slice($daynames, $this->getWeekStart()), array_slice($daynames, 0, $this->getWeekStart()));
		$this->addTempVar('weekdays', $daynames);
		
		$date_description = $p4a->i18n->datetime->format($this->getStartDate(), '%B %Y');
		$this->toolbar->buttons->date_description->setValue($date_description);
		
		if (is_object($this->_source)) {
			$events = $this->_source->getAll();
		} else {
			$events = array();
		}
		
		switch ($this->getType()) {
			case 'month':
				return $this->getAsMonth($events);
			case 'week':
				return $this->getAsWeek($events);
			case 'day':
				return $this->getAsDay($events);
		}
	}
}

?>