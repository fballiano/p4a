<?php

/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */

	/**
	 * Date/Time Class.
	 * Library for standard date operations like formatting/unformatting.<br><br>
	 * Some parts of the code are based on PEAR::Date and PEAR::Date_Calc packages at {link http://pear.php.net/package-info.php?package=Date},
	 * modified for p4a necessity.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Date
	{
	    /**
	     *  Date pretty printing, similar to strftime()
	     *
	     *  Formats the date in the given format, much like
	     *  strftime().  Most strftime() options are supported.<br><br>
	     *
	     *  formatting options:<br><br>
	     *
	     *  %a    abbreviated weekday name (Sun, Mon, Tue) <br>
	     *  %A    full weekday name (Sunday, Monday, Tuesday) <br>
	     *  %b    abbreviated month name (Jan, Feb, Mar) <br>
	     *  %B    full month name (January, February, March) <br>
	     *  %C    century number (the year divided by 100 and truncated to an integer, range 00 to 99) <br>
	     *  %d    day of month (range 01 to 31) <br>
	     *  %D    same as "%m/%d/%y" <br>
	     *  %e    day of month, single digit (range 0 to 31) <br>
	     *  %E    number of days since unspecified epoch (integer, Date_Calc::dateToDays()) <br>
	     *  %H    hour as decimal number (00 to 23) <br>
	     *  %I    hour as decimal number on 12-hour clock (01 to 12) <br>
	     *  %j    day of year (range 001 to 366) <br>
	     *  %m    month as decimal number (range 01 to 12) <br>
	     *  %M    minute as a decimal number (00 to 59) <br>
	     *  %n    newline character (\n) <br>
	     *  %O    dst-corrected timezone offset expressed as "+/-HH:MM" <br>
	     *  %o    raw timezone offset expressed as "+/-HH:MM" <br>
	     *  %p    either 'am' or 'pm' depending on the time <br>
	     *  %P    either 'AM' or 'PM' depending on the time <br>
	     *  %r    time in am/pm notation, same as "%I:%M:%S %p" <br>
	     *  %R    time in 24-hour notation, same as "%H:%M" <br>
	     *  %S    seconds as a decimal number (00 to 59) <br>
	     *  %t    tab character (\t) <br>
	     *  %T    current time, same as "%H:%M:%S" <br>
	     *  %w    weekday as decimal (0 = Sunday) <br>
	     *  %U    week number of current year, first sunday as first week <br>
	     *  %y    year as decimal (range 00 to 99) <br>
	     *  %Y    year as decimal including century (range 0000 to 9999) <br>
	     *  %%    literal '%' <br>
	     * <br>
	     *
	     * @access public
	     * @param string date in standard date/time format
	     * @param string the desidered formatting format
	     * @param array localization strings
	     * @return string date/time in given format
	     */
	    function format($date = NULL, $format, $locale_vars = NULL)
	    {
	    	if (empty($date)) {
	    		return '';
	    	}

	    	$aDate = P4A_Date::parse($date);
	        $output = "";
			
			if (preg_match("/%[pP]/", $format)) {
				if ($aDate['hour'] < 12) {
					$aDate['am_pm'] = 'AM';
				} else {
					$aDate['hour'] -= 12;
					$aDate['am_pm'] = 'PM';
				}
			}

	        for($strpos = 0; $strpos < strlen($format); $strpos++) {
	            $char = substr($format,$strpos,1);
	            if ($char == "%") {
	                $nextchar = substr($format,$strpos + 1,1);
	                switch ($nextchar) {
	                case "a":
	                    $output .= P4A_Date::get_weekday_short_name($aDate['year'], $aDate['month'], $aDate['day'], $locale_vars['days']);
	                    break;
	                case "A":
	                    $output .= P4A_Date::get_weekday_name($aDate['year'], $aDate['month'], $aDate['day'], $locale_vars['days']);
	                    break;
	                case "b":
	                    $output .= P4A_Date::get_month_short_name($aDate['month'], $locale_vars['months']);
	                    break;
	                case "B":
	                    $output .= P4A_Date::get_month_name($aDate['month'], $locale_vars['months']);
	                    break;
	                case "C":
	                    $output .= sprintf("%02d",intval($aDate['year']/100));
	                    break;
	                case "d":
	                    $output .= sprintf("%02d",$aDate['day']);
	                    break;
	                case "D":
	                    $output .= sprintf("%02d/%02d/%02d",$aDate['month'],$aDate['day'],$aDate['year']);
	                    break;
	                case "e":
	                    $output .= $aDate['day'];
	                    break;
	                case "E":
	                    $output .= P4A_Date::date_to_days($aDate['year'],$aDate['month'],$aDate['day']);
	                    break;
					case "l":
	                case "H":
	                    $output .= sprintf("%02d", $aDate['hour']);
	                    break;
	                case "I":
	                    $hour = ($aDate['hour'] + 1) > 12 ? $aDate['hour'] - 12 : $aDate['hour'];
	                    $output .= sprintf("%02d", $hour==0 ? 12 : $hour);
	                    break;
	                case "j":
	                    $output .= P4A_Date::julian_date($aDate['year'],$aDate['month'],$aDate['day']);
	                    break;
	                case "m":
	                    $output .= sprintf("%02d",$aDate['month']);
	                    break;
	                case "M":
	                    $output .= sprintf("%02d",$aDate['minute']);
	                    break;
	                case "n":
	                    $output .= "\n";
	                    break;
	                case "O":
	                	p4a_error("P4A_Date::format(): timezone operation are not implemented.");
	                    $offms = $this->tz->getOffset($this);
	                    $direction = $offms >= 0 ? "+" : "-";
	                    $offmins = abs($offms) / 1000 / 60;
	                    $hours = $offmins / 60;
	                    $minutes = $offmins % 60;
	                    $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
	                    break;
	                case "o":
	                	p4a_error("P4A_Date::format(): timezone operation are not implemented.");
	                    $offms = $this->tz->getRawOffset($this);
	                    $direction = $offms >= 0 ? "+" : "-";
	                    $offmins = abs($offms) / 1000 / 60;
	                    $hours = $offmins / 60;
	                    $minutes = $offmins % 60;
	                    $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
	                    break;
	                case "p":
	                    $output .= $aDate['am_pm'];
	                    break;
	                case "r":
	                    $hour = ($aDate['hour'] + 1) > 12 ? $aDate['hour'] - 12 : $aDate['hour'];
	                    $output .= sprintf("%02d:%02d:%02d %s", $hour==0 ?  12 : $hour, $aDate['minute'], $aDate['second'], $aDate['hour'] >= 12 ? "PM" : "AM");
	                    break;
	                case "R":
	                    $output .= sprintf("%02d:%02d", $aDate['hour'], $aDate['minute']);
	                    break;
	                case "S":
	                    $output .= sprintf("%02d", $aDate['second']);
	                    break;
	                case "t":
	                    $output .= "\t";
	                    break;
	                case "T":
	                    $output .= sprintf("%02d:%02d:%02d", $aDate['hour'], $aDate['minute'], $aDate['second']);
	                    break;
	                case "w":
	                    $output .= P4A_Date::day_of_week($aDate['year'],$aDate['month'],$aDate['day']);
	                    break;
	                case "U":
	                    $output .= P4A_Date::week_of_year($aDate['year'],$aDate['month'],$aDate['day']);
	                    break;
	                case "y":
	                    $output .= substr($aDate['year'],2,2);
	                    break;
	                case "Y":
	                    $output .= $aDate['year'];
	                    break;
	                case "Z":
	                    p4a_error("P4A_Date::format(): timezone operation are not implemented.");
	                    $output .= $this->tz->inDaylightTime($this) ? $this->tz->getDSTShortName() : $this->tz->getShortName();
	                    break;
	                case "%":
	                    $output .= "%";
	                    break;
	                default:
	                    $output .= $char.$nextchar;
	                }
	                $strpos++;
	            } else {
	                $output .= $char;
	            }
	        }
	        return $output;
	    }

	    /**
	     * Reverse date analysis on a formatted date.
	     * This function takes the formatted date and the original format
	     * and try to get it back in standard datetime format.
	     * @access public
	     * @param string formatted date
	     * @param string the format
	     * @param string the output format
	     * @param array localization strings
	     * @return string
	     */
	    function unformat($date, $format, $output_format = P4A_DATETIME, $locale_vars = NULL)
	    {
	        $regexp = "";
	        $map = array();
	        $nucleus_counter = 1;

	    	$iso			= array();
	    	$iso['year']	= 0;
	    	$iso['month']	= 0;
	    	$iso['day']		= 0;
	    	$iso['hour']	= 0;
	    	$iso['minute']	= 0;
	    	$iso['second']	= 0;

	        for ($strpos = 0; $strpos < strlen($format); $strpos++) {
	            $char = substr($format,$strpos,1);
	            if ($char == "%") {
	                $nextchar = substr($format, $strpos + 1, 1);
	                switch ($nextchar) {
	                case "a":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= P4A_Date::get_weekday_short_name($aDate['year'], $aDate['month'], $aDate['day'], $locale_vars);
	                    break;
	                case "A":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= P4A_Date::get_weekday_name($aDate['year'], $aDate['month'], $aDate['day'], $locale_vars);
	                    break;
	                case "b":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= P4A_Date::get_month_short_name($aDate['month'], $locale_vars);
	                    break;
	                case "B":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= P4A_Date::get_month_name($aDate['month'], $locale_vars);
	                    break;
	                case "C":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= sprintf("%02d",intval($aDate['year']/100));
	                    break;
	                case "d":
	                    $regexp .= '([0-9]{1,2})';
	                    $map['day'] = $nucleus_counter++;
	                    break;
	                case "D":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= sprintf("%02d/%02d/%02d",$aDate['month'],$aDate['day'],$aDate['year']);
	                    break;
	                case "e":
	                    $regexp .= '([0-9]{1,2})';
	                    $map['day'] = $nucleus_counter++;
	                    break;
	                case "E":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= P4A_Date::date_to_days($aDate['year'],$aDate['month'],$aDate['day']);
	                    break;
					case "l":
	                case "H":
	                    $regexp .= '([0-9]{1,2})';
	                    $map['hour'] = $nucleus_counter++;
	                    break;
	                case "I":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $hour = ($aDate['hour'] + 1) > 12 ? $aDate['hour'] - 12 : $aDate['hour'];
	                    $output .= sprintf("%02d", $hour==0 ? 12 : $hour);
	                    break;
	                case "j":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= P4A_Date::julian_date($aDate['year'],$aDate['month'],$aDate['day']);
	                    break;
					case "m":
	                    $regexp .= '([0-9]{1,2})';
	                    $map['month'] = $nucleus_counter++;
	                    break;
	                case "M":
	                    $regexp .= '([0-9]{1,2})';
	                    $map['minute'] = $nucleus_counter++;
	                    break;
	                case "n":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= "\n";
	                    break;
	                case "O":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $offms = $this->tz->getOffset($this);
	                    $direction = $offms >= 0 ? "+" : "-";
	                    $offmins = abs($offms) / 1000 / 60;
	                    $hours = $offmins / 60;
	                    $minutes = $offmins % 60;
	                    $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
	                    break;
	                case "o":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $offms = $this->tz->getRawOffset($this);
	                    $direction = $offms >= 0 ? "+" : "-";
	                    $offmins = abs($offms) / 1000 / 60;
	                    $hours = $offmins / 60;
	                    $minutes = $offmins % 60;
	                    $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
	                    break;
					case "P":
	                case "p":
						$regexp .= '(am|AM|aM|Am|pm|PM|pM|Pm)?';
						$map['am_pm'] = $nucleus_counter++;
	                    break;
	                case "r":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $hour = ($aDate['hour'] + 1) > 12 ? $aDate['hour'] - 12 : $aDate['hour'];
	                    $output .= sprintf("%02d:%02d:%02d %s", $hour==0 ?  12 : $hour, $aDate['minute'], $aDate['second'], $aDate['hour'] >= 12 ? "PM" : "AM");
	                    break;
	                case "R":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= sprintf("%02d:%02d", $aDate['hour'], $aDate['minute']);
	                    break;
	                case "S":
	                    $regexp .= '([0-9]{1,2})';
	                    $map['second'] = $nucleus_counter++;
	                    break;
	                case "t":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= "\t";
	                    break;
	                case "T":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= sprintf("%02d:%02d:%02d", $aDate['hour'], $aDate['minute'], $aDate['second']);
	                    break;
	                case "w":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= P4A_Date::day_of_week($aDate['year'],$aDate['month'],$aDate['day']);
	                    break;
	                case "U":
	                	p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= P4A_Date::week_of_year($aDate['year'],$aDate['month'],$aDate['day']);
	                    break;
	                case "y":
	                    $regexp .= '([0-9]{1,4})';
	                    $map['year'] = $nucleus_counter++;
	                    break;
	                case "Y":
	                    $regexp .= '([0-9]{1,4})';
	                    $map['year'] = $nucleus_counter++;
	                    break;
	                case "Z":
	                    p4a_error("P4A_Date::unformat(): reverse formatting with '%$nextchar' in format is not implemented.");
	                    $output .= $this->tz->inDaylightTime($this) ? $this->tz->getDSTShortName() : $this->tz->getShortName();
	                    break;
	                case "%":
	                    $output .= "%";
	                    break;
	                default:
	                    $regexp .= $char.$nextchar;
	                }
	                $strpos++;
	            } elseif ($char == '/' or $char == '-') {
	            	$regexp .= '[\/-]';
	            } elseif ($char == '.' or $char == ':') {
	            	$regexp .= '[\.:]';
				} elseif ($char == ' ') {
					$regexp .= '\s*';
	            } else {
	                $regexp .= $char;
	            }
	        }
			
			$regexp = trim($regexp);
	        if (preg_match("/$regexp/", $date, $res)) {
	        	foreach ($map as $key=>$nucleus) {
					if ($key == 'am_pm') {
						if (strtolower($res[$nucleus]) == 'pm') {
							$iso['hour'] += 12;
						}
						continue;
					}
					$iso[$key] += $res[$nucleus];
	        	}
	        }
			
			if ($iso['month'] == 0) $iso['month'] = 1;
	    	if ($iso['day'] == 0) $iso['day'] = 1;
			
			$iso['month'] = str_pad($iso['month'], 2, 0, STR_PAD_LEFT);
			$iso['day'] = str_pad($iso['day'], 2, 0, STR_PAD_LEFT);

	        if (defined('P4A_DATETIME') and ($output_format == P4A_DATETIME)) {
	        	return "{$iso['year']}-{$iso['month']}-{$iso['day']} {$iso['hour']}:{$iso['minute']}:{$iso['second']}";
	        } elseif (defined('P4A_DATE') and ($output_format == P4A_DATE)) {
	        	return "{$iso['year']}-{$iso['month']}-{$iso['day']}";
	        } else {
	        	return P4A_Date::format("{$iso['year']}-{$iso['month']}-{$iso['day']} {$iso['hour']}:{$iso['minute']}:{$iso['second']}", $output_format, $locale_vars);
	        }
	    }

	    /**
	     * Parses a date in the standard datetime format and returns
	     * an associative array with every single date part.
	     * @access public
	     * @param string the date
	     * @return array
	     */
	    function parse($date)
	    {
	    	$return				= array();
	    	$return['year']		= '0';
	    	$return['month']	= '01';
	    	$return['day']		= '01';
	    	$return['hour']		= '00';
	    	$return['minute']	= '00';
	    	$return['second']	= '00';

            if (ereg("([0-9]{1,4})", $date, $regs)) {
            	$return['year'] = $regs[1];
            } else {
            	return $return;
	    	}

            if (ereg("[0-9]{1,4}-([0-9]{1,2})", $date, $regs)) {
            	$return['month'] = sprintf("%02d", $regs[1]);
            } else {
            	return $return;
	    	}

            if (ereg("[0-9]{1,4}-[0-9]{1,2}-([0-9]{1,2})", $date, $regs)) {
            	$return['day'] = sprintf("%02d", $regs[1]);
            } else {
            	return $return;
	    	}

            if (ereg("[0-9]{1,4}-[0-9]{1,2}-[0-9]{1,2}[ ]([0-9]{1,2})", $date, $regs)) {
            	$return['hour'] = sprintf("%02d", $regs[1]);
            } else {
            	return $return;
	    	}

            if (ereg("[0-9]{1,4}-[0-9]{1,2}-[0-9]{1,2}[ ][0-9]{1,2}:([0-9]{1,2})", $date, $regs)) {
            	$return['minute'] = sprintf("%02d",$regs[1]);
            } else {
            	return $return;
	    	}

            if (ereg("[0-9]{1,4}-[0-9]{1,2}-[0-9]{1,2}[ ][0-9]{1,2}:[0-9]{1,2}:([0-9]{1,2})", $date, $regs)) {
            	$return['second'] = sprintf("%02d",$regs[1]);
            } else {
            	return $return;
	    	}

            return $return;
	    }

	    /**
	     * Returns true for valid date, false for invalid date.
		 * @access public
	     * @param string year in format YYYY
	     * @param string month in format MM
	     * @param string day in format DD
	     * @return boolean
	     */
	    function is_valid_date($year, $month, $day)
	    {
	        if ($year < 0 or $year > 9999) {
	            return false;
	        }

	        if (!checkdate($month,$day,$year)) {
	            return false;
	        }

	        return true;
	    }

	    /**
	     * Returns the abbreviated weekday name for the given date
	     * @access public
	     * @param string year in format YYYY, default is current local year
	     * @param string month in format MM, default is current local month
	     * @param string day in format DD, default is current local day
	     * @param array localization strings for days names
	     * @return string full month name
	     * @see P4A_Date::get_weekday_name
	     */
	    function get_weekday_short_name($year = NULL, $month = NULL, $day = NULL, $locale_vars = NULL)
	    {
	    	return substr(P4A_Date::get_weekday_name($year, $month, $day, $locale_vars), 0, 3);
	    }

	    /**
	     * Returns the full weekday name for the given date
	     * @access public
	     * @param string year in format YYYY, default is current local year
	     * @param string month in format MM, default is current local month
	     * @param string day in format DD, default is current local day
	     * @param array localization strings for days names
	     * @return string full month name
	     * @see P4A_Date::get_weekday_name
	     */
	    function get_weekday_name($year = NULL, $month = NULL, $day = NULL, $locale_vars = NULL)
	    {
	        $weekday = P4A_Date::day_of_week($year, $month, $day);

			if ($locale_vars === NULL) {
				$locale_vars = P4A_Date::get_weekdays_names();
			}

	        return $locale_vars[$weekday];
	    }

	    /**
	     * Returns day of week for given date, 0=Sunday
	     * @access public
		 * @param string year in format YYYY, default is current local year
	     * @param string month in format MM, default is current local month
	     * @param string day in format DD, default is current local day
	     * @return int $weekday_number
	     */
	    function day_of_week($year = NULL, $month = NULL, $day = NULL)
	    {
	        if (empty($year)) {
	            $year = P4A_Date::now('%Y');
	        }
	        if (empty($month)) {
	            $month = P4A_Date::now('%m');
	        }
	        if (empty($day)) {
	            $day = P4A_Date::now('%d');
	        }

	        if ($month > 2) {
	            $month -= 2;
	        } else {
	            $month += 10;
	            $year--;
	        }

	        $day = ( floor((13 * $month - 1) / 5) +
	            $day + ($year % 100) +
	            floor(($year % 100) / 4) +
	            floor(($year / 100) / 4) - 2 *
	            floor($year / 100) + 77);

	        $weekday_number = (($day - 7 * floor($day / 7)));
	        return $weekday_number;
	    }

	    /**
	     * Returns the current local date. NOTE: This function
	     * retrieves the local date using strftime(), which may
	     * or may not be 32-bit safe on your system.
	     * @access public
	     * @param string the strftime() format to return the date
	     * @return string the current date in specified format
	     */
	    function now($format='%Y%m%d')
	    {
	        return(strftime($format,time()));
	    }

	    /**
	    * Returns an array of week days
	    * Used to take advantage of the setlocale function to
	    * return language specific week days
	    * @access public
	    * @returns array An array of week day names
	    */
	    function get_weekdays_names()
	    {
	        for($i=0;$i<7;$i++) {
	            $weekdays[$i] = ucfirst(strftime('%A', mktime(0, 0, 0, 1, $i, 2001)));
	        }
	        return($weekdays);
	    }

	    /**
	     * Returns the full month name for the given month
	     * @access public
	     * @param string month in format MM
	     * @param array localization strings for months names
	     * @return string full month name
	     */
	    function get_month_name($month = NULL, $locale_vars = NULL)
	    {
	        if (empty($month)) {
	            $month = P4A_Date::now('%m');
	        }

	        $month = (int)$month;

			if( $locale_vars === NULL ) {
	        	$locale_vars = P4A_Date::get_months_names();
			}

	        return $locale_vars[$month-1];
	    }

	    /**
	     * Returns the abbreviated month name for the given month
	     * @access public
	     * @param string month in format MM
	     * @param array localization strings for months names
	     * @return string abbreviated month name
	     * @see P4A_Date::get_month_name
	     */
	    function get_month_short_name($month = NULL, $locale_vars = NULL)
	    {
	        return substr(P4A_Date::get_month_name($month, $locale_vars), 0, 3);
	    }

	    /**
	     * Returns an array of month names
	     * Used to take advantage of the setlocale function to return
	     * language specific month names.
	     * @returns array An array of month names
	     */
	    function get_months_names()
	    {
	    	$months = array();
	        for($i=1;$i<13;$i++) {
	            $months[] = ucfirst(strftime('%B', mktime(0, 0, 0, $i, 1, 2001)));
	        }
	        return($months);
	    }

	    /**
	     * Converts a date to number of days since a
	     * distant unspecified epoch.
		 * @access public
		 * @param string year in format YYYY
		 * @param string month in format MM
	     * @param string day in format DD
	     * @return integer number of days
	     */
	    function date_to_days($year = NULL, $month = NULL, $day = NULL)
	    {
	        if (empty($year)) {
	            $year = P4A_Date::now('%Y');
	        }
	        if (empty($month)) {
	            $month = P4A_Date::now('%m');
	        }
	        if (empty($day)) {
	            $day = P4A_Date::now('%d');
	        }

	        $century = (int) substr($year,0,2);
	        $year = (int) substr($year,2,2);

	        if ($month > 2) {
	            $month -= 3;
	        } else {
	            $month += 9;
	            if ($year) {
	                $year--;
	            } else {
	                $year = 99;
	                $century --;
	            }
	        }

	        return (floor(( 146097 * $century) / 4 ) +
	            floor(( 1461 * $year) / 4 ) +
	            floor(( 153 * $month + 2) / 5 ) +
	            $day + 1721119);
	    }

	    /**
	     * Converts number of days to a distant unspecified epoch.
	     * @access public
	     * @param int number of days
	     * @param string format for returned date
	     * @param array localization strings for months names
	     * @return string date in specified format
	     */
	    function days_to_date($days, $format='%Y%m%d', $locale_vars = NULL)
	    {

	        $days       -=  1721119;
	        $century    =   floor(( 4 * $days - 1) / 146097);
	        $days       =   floor(4 * $days - 1 - 146097 * $century);
	        $day        =   floor($days / 4);

	        $year       =   floor(( 4 * $day +  3) / 1461);
	        $day        =   floor(4 * $day +  3 - 1461 * $year);
	        $day        =   floor(($day +  4) / 4);

	        $month      =   floor(( 5 * $day - 3) / 153);
	        $day        =   floor(5 * $day - 3 - 153 * $month);
	        $day        =   floor(($day +  5) /  5);

	        if ($month < 10) {
	            $month +=3;
	        } else {
	            $month -=9;
	            if ($year++ == 99) {
	                $year = 0;
	                $century++;
	            }
	        }

	        $century = sprintf('%02d',$century);
	        $year = sprintf('%02d',$year);
	        return(P4A_Date::format($century.$year.'-'.$month.'-'.$day, $format, $locale_vars));
	    }

	    /**
	     * Returns number of days since 31 December of year before given date.
	     * @access public
	     * @param string year in format YYYY, default is current local year
	     * @param string month in format MM, default is current local month
	     * @param string day in format DD, default is current local day
	     * @return int $julian
	     */
	    function julian_date($year = NULL, $month = NULL, $day = NULL)
	    {
	        if (empty($year)) {
	            $year = P4A_Date::now('%Y');
	        }
	        if (empty($month)) {
	            $month = P4A_Date::now('%m');
	        }
	        if (empty($day)) {
	            $day = P4A_Date::now('%d');
	        }

	        $year = (int) $year;
	        $month = (int) $month;
	        $day = (int) $day;

	        $days = array(0,31,59,90,120,151,181,212,243,273,304,334);

	        $julian = ($days[$month - 1] + $day);

	        if ($month > 2 && P4A_Date::is_leap_year($year)) {
	            $julian++;
	        }

	        return($julian);
	    }

	     /**
	     * Returns true for a leap year, else false
	     * @access public
	     * @param string year in format YYYY
	     * @return boolean
	     */

	    function is_leap_year($year = NULL)
	    {
	        if (empty($year)) {
	            $year = P4A_Date::now('%Y');
	        }

	        if (strlen($year) != 4) {
	            return false;
	        }

	        if (preg_match('/\D/',$year)) {
	            return false;
	        }

	        return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0);
	    }

	    /**
	     * Returns week of the year, first Sunday is first day of first week
	     * @access public
	     * @param string year in format YYYY, default is current local year
	     * @param string month in format MM, default is current local month
	     * @param string day in format DD, default is current local day
	     * @return integer $week_number
	     */
	    function week_of_year($year = NULL, $month = NULL, $day = NULL)
	    {
	        $iso    = P4A_Date::gregorian_to_iso($year, $month, $day );
	        $parts  = explode('-',$iso);
	        $week_number = intval($parts[1]);
	        return $week_number;
	    }

	    /**
	     * Converts from Gregorian Year-Month-Day to
	     * ISO YearNumber-WeekNumber-WeekDay
	     * Uses ISO 8601 definitions.
	     * Algorithm from Rick McCarty, 1999 at
	     * http://personal.ecu.edu/mccartyr/ISOwdALG.txt.
	     * Transcribed to PHP by Jesus M. Castagnetto
	     * @access public
	     * @param string day in format DD
	     * @param string month in format MM
	     * @param string year in format CCYY
	     * @return string
	     */
	    function gregorian_to_iso($year, $month, $day)
	    {
	        if (empty($year)) {
	            $year = P4A_Date::now('%Y');
	        }
	        if (empty($month)) {
	            $month = P4A_Date::now('%m');
	        }
	        if (empty($day)) {
	            $day = P4A_Date::now('%d');
	        }

	        $year = (int) $year;
	        $month = (int) $month;
	        $day = (int) $day;

	        $mnth = array (0,31,59,90,120,151,181,212,243,273,304,334);
	        $y_isleap = P4A_Date::is_leap_year($year);
	        $y_1_isleap = P4A_Date::is_leap_year($year - 1);
	        $day_of_year_number = $day + $mnth[$month - 1];
	        if ($y_isleap && $month > 2) {
	            $day_of_year_number++;
	        }
	        // find Jan 1 weekday (monday = 1, sunday = 7)
	        $yy = ($year - 1) % 100;
	        $c = ($year - 1) - $yy;
	        $g = $yy + intval($yy/4);
	        $jan1_weekday = 1 + intval((((($c / 100) % 4) * 5) + $g) % 7);
	        // weekday for year-month-day
	        $h = $day_of_year_number + ($jan1_weekday - 1);
	        $weekday = 1 + intval(($h - 1) % 7);
	        // find if Y M D falls in YearNumber Y-1, WeekNumber 52 or
	        if ($day_of_year_number <= (8 - $jan1_weekday) && $jan1_weekday > 4){
	            $yearnumber = $year - 1;
	            if ($jan1_weekday == 5 || ($jan1_weekday == 6 && $y_1_isleap)) {
	                $weeknumber = 53;
	            } else {
	                $weeknumber = 52;
	            }
	        } else {
	            $yearnumber = $year;
	        }
	        // find if Y M D falls in YearNumber Y+1, WeekNumber 1
	        if ($yearnumber == $year) {
	            if ($y_isleap) {
	                $i = 366;
	            } else {
	                $i = 365;
	            }
	            if (($i - $day_of_year_number) < (4 - $weekday)) {
	                $yearnumber++;
	                $weeknumber = 1;
	            }
	        }
	        // find if Y M D falls in YearNumber Y, WeekNumber 1 through 53
	        if ($yearnumber == $year) {
	            $j = $day_of_year_number + (7 - $weekday) + ($jan1_weekday - 1);
	            //$weeknumber = intval($j / 7) + 1; // kludge!!! - JMC
	            $weeknumber = intval($j / 7); // kludge!!! - JMC
	            if ($jan1_weekday > 4) {
	                $weeknumber--;
	            }
	        }
	        // put it all together
	        if ($weeknumber < 10)
	            $weeknumber = '0'.$weeknumber;
	        return "{$yearnumber}-{$weeknumber}-{$weekday}";
	    }

	    /**
	     * Determines if given date is a future date from now.
	     * @access public
	     * @param string year in format YYYY
	     * @param string month in format MM
	     * @param string day in format DD
	     * @return boolean
	     */
	    function is_future_date($year, $month, $day)
	    {
	        $this_year = P4A_Date::now('%Y');
	        $this_month = P4A_Date::now('%m');
	        $this_day = P4A_Date::now('%d');

	        if ($year > $this_year) {
	            return true;
	        } elseif ($year == $this_year) {
	            if ($month > $this_month) {
	                return true;
	            } elseif ($month == $this_month) {
	                if ($day > $this_day) {
	                    return true;
	                }
	            }
	        }

	        return false;
	    }

	    /**
	     * Determines if given date is a past date from now.
	     * @access public
	     * @param string year in format YYYY
	     * @param string month in format MM
	     * @param string day in format DD
	     * @return boolean
	     */
	    function is_past_date($year, $month, $day)
	    {
	        $this_year = P4A_Date::now('%Y');
	        $this_month = P4A_Date::now('%m');
	        $this_day = P4A_Date::now('%d');

	        if ($year < $this_year) {
	            return true;
	        } elseif ($year == $this_year) {
	            if ($month < $this_month) {
	                return true;
	            } elseif ($month == $this_month) {
	                if ($day < $this_day) {
	                    return true;
	                }
	            }
	        }

	        return false;
	    }
	}

?>