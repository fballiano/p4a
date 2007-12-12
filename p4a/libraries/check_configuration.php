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
    $error .= "<div class='box'>Checking SERVER OPERATING SYSTEM:<br/>";
    if (_DS_ == '/') {
    	$error .= "P4A is configured as running on <b>Linux</b>, if your server operating system is different, than correct P4A_OS and _DS_ definition.";
    } else {
    	$error .= "P4A is configured as running on <b>Windows</b>, if your server operating system is different, than correct P4A_OS and _DS_ definition.";
    }
    $error .= "</div>\n";

    // DOCUMENT ROOT
    $error .= "<div class='box'>Checking DOCUMENT_ROOT: ";
    if (strlen(P4A_SERVER_DIR) == 0) {
    	$error .= "<span class='red'>FAILED</span><br/>Define P4A_SERVER_DIR as your DOCUMENT_ROOT.";
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
		if (@System::mkDir("-p " . P4A_UPLOADS_DIR)) {
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
    	$error .= "<span class='red'>FAILED</span><br/>Create \"" . P4A_UPLOADS_DIR . "\" and set it writable.";
    	$correct = false;
    }
    $error .= "</div>";

    // UPLOADS TEMPORARY DIRECTORY
    $error .= "<div class='box'>Checking UPLOADS TEMPORARY DIRECTORY: ";

	if (is_dir(P4A_UPLOADS_TMP_DIR) and is_writable(P4A_UPLOADS_TMP_DIR)) {
		$ok = true;
	} elseif (!is_dir(P4A_UPLOADS_TMP_DIR)) {
		if (@System::mkDir("-p " . P4A_UPLOADS_TMP_DIR)) {
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
    	$error .= "<span class='red'>FAILED</span><br/>Create \"" . P4A_UPLOADS_TMP_DIR . "\" and set it writable.";
    	$correct = false;
    }
    $error .= "</div>";

    // ADDITIONAL DIRECTORY
	if ($additionalDir) {
		$error .= "<div class='box'>Checking ADDITIONAL DIRECTORY: ";

		if (is_dir($additionalDir) and is_writable($additionalDir)) {
			$ok = true;
		} elseif (!is_dir($additionalDir)) {
			if (@System::mkDir("-p " . $additionalDir)) {
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
			$error .= "<span class='red'>FAILED</span><br/>Create \"$additionalDir\" and set it writable.";
			$correct = false;
		}
		$error .= "</div>";
	}

    // DATABASE CONNECTION
    $error .= "<div class='box'>Checking DATABASE CONNECTION: ";
    if (defined('P4A_DSN')) {
    	try {
    		$db =& P4A_DB::singleton(P4A_DSN);
    		$db->adapter->getConnection();
    		$error .= "<span class='green'>OK</span>";
    	} catch (Exception $e) {
    		$error .= "<span class='red'>FAILED</span><br/>Check P4A_DSN definition.";
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