<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/agpl.html>.
 * 
 * To contact the authors write to:									<br />
 * CreaLabs SNC														<br />
 * Via Medail, 32													<br />
 * 10144 Torino (Italy)												<br />
 * Website: {@link http://www.crealabs.it}							<br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

/*
 * Checks and tries to repair system configuration.
 * @access public
 * @param string	If you want to check for another writable directory use this param
 * @return boolean TRUE on success, an error string on failure
 */
function p4a_check_configuration($additionalDir = null)
{
    $correct = true;
    $title = "Configuration checks for \"" . P4A_APPLICATION_NAME . "\"";
    $error = "<center><h2>$title</h2></center>\n" ;

    // OPERATING SYSTEM
    $error .= "<div class='box'>Checking SERVER OPERATING SYSTEM:<br />";
    if (_DS_ == '/') {
    	$error .= "P4A is configured as running on <b>Linux</b>, if your server operating system is different, than correct P4A_OS and _DS_ definition.";
    } else {
    	$error .= "P4A is configured as running on <b>Windows</b>, if your server operating system is different, than correct P4A_OS and _DS_ definition.";
    }
    $error .= "</div>\n";
    
    // PHP VERSION
    $error .= "<div class='box'>Checking PHP VERSION: ";
    $phpversion = explode('-', PHP_VERSION);
    $phpversion = explode('.', $phpversion[0]);
    
    if ($phpversion[0] < 5 or ($phpversion[0] == 5 and $phpversion[1] < 2)) {
    	$error .= "<span class='red'>{$phpversion[0]}.{$phpversion[1]}.{$phpversion[2]}</span><br />PHP 5.2.0 (or higher) is required in order to run P4A";
    	$correct = false;
    } else {
    	$error .= "<span class='green'>{$phpversion[0]}.{$phpversion[1]}.{$phpversion[2]}</span>";
    }
    $error .= "</div>\n";

    // DOCUMENT ROOT
    $error .= "<div class='box'>Checking DOCUMENT_ROOT: ";
    if (strlen(P4A_SERVER_DIR) == 0) {
    	$error .= "<span class='red'>FAILED</span><br />Define P4A_SERVER_DIR as your DOCUMENT_ROOT.";
    	$correct = false;
    } else {
    	$error .= "<span class='green'>OK</span>";
    }
    $error .= "</div>";

    // UPLOADS DIRECTORY
    $error .= "<div class='box'>Checking UPLOADS DIRECTORY: ";

	if (is_dir(P4A_UPLOADS_DIR) and is_writable(P4A_UPLOADS_DIR)) {
		$ok = true;
	} elseif (!is_dir(P4A_UPLOADS_DIR)) {
		if (P4A_Mkdir_Recursive(P4A_UPLOADS_DIR)) {
			$ok = true;
		} else {
			$ok = false;
		}
	} else {
		$ok = false;
	}

    if ($ok) {
    	$error .= "<span class='green'>OK</span>";
    } else {
    	$error .= "<span class='red'>FAILED</span><br />Create \"" . P4A_UPLOADS_DIR . "\" and set it writable.";
    	$correct = false;
    }
    $error .= "</div>";

    // UPLOADS TEMPORARY DIRECTORY
    $error .= "<div class='box'>Checking UPLOADS TEMPORARY DIRECTORY: ";

	if (is_dir(P4A_UPLOADS_TMP_DIR) and is_writable(P4A_UPLOADS_TMP_DIR)) {
		$ok = true;
	} elseif (!is_dir(P4A_UPLOADS_TMP_DIR)) {
		if (P4A_Mkdir_Recursive(P4A_UPLOADS_TMP_DIR)) {
			$ok = true;
		} else {
			$ok = false;
		}
	} else {
		$ok = false;
	}

    if ($ok) {
    	$error .= "<span class='green'>OK</span>";
    } else {
    	$error .= "<span class='red'>FAILED</span><br />Create \"" . P4A_UPLOADS_TMP_DIR . "\" and set it writable.";
    	$correct = false;
    }
    $error .= "</div>";

    // ADDITIONAL DIRECTORY
	if ($additionalDir) {
		$error .= "<div class='box'>Checking ADDITIONAL DIRECTORY: ";

		if (is_dir($additionalDir) and is_writable($additionalDir)) {
			$ok = true;
		} elseif (!is_dir($additionalDir)) {
			if (P4A_Mkdir_Recursive($additionalDir)) {
				$ok = true;
			} else {
				$ok = false;
			}
		} else {
			$ok = false;
		}

		if ($ok) {
			$error .= "<span class='green'>OK</span>";
		} else {
			$error .= "<span class='red'>FAILED</span><br />Create \"$additionalDir\" and set it writable.";
			$correct = false;
		}
		$error .= "</div>";
	}

    // DATABASE CONNECTION
    $error .= "<div class='box'>Checking DATABASE CONNECTION: ";
    if (defined('P4A_DSN')) {
    	try {
    		P4A_DB::singleton(P4A_DSN)->adapter->getConnection();
    		$error .= "<span class='green'>OK</span>";
    	} catch (Exception $e) {
    		$error .= "<span class='red'>FAILED</span><br />Error: " . $e->getMessage() . "<br />Check P4A_DSN definition.";
    		$correct = false;
    	}
    } else {
    	$error .= "P4A_DSN is not defined, no database connection.";
    }
    $error .= "</div>";

	// FINAL STRINGS
	$style = "<style>body {font-family:sans-serif; font-size:90%; color:#111} h1,h2,h3,h4{text-align:center} .box{padding:10px; border:1px solid #111; background-color:#fafafa; margin-bottom:10px;} .red{color:red;font-weight:bold} .green{color:green;font-weight:bold}</style>";
	$error = "<html><head><title>{$title}</title></head><body>{$style}{$error}</body></html>";

    if ($correct) {
		return true;
    } else {
		return $error;
	}
}