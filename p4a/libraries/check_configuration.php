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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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
 
function check_configuration( &$error )
{
    require_once( dirname(__FILE__) . '/../include.php' );
    
    $correct = true;
    $error = "<center><h2>Application \"" . P4A_APPLICATION_NAME . "\" - Configuration Check</h2></center>\n" ;
    
    $error .= "<h3>Activities:</h3>\n";
    
    // OPERATING SYSTEM
    $error .= "Checking SERVER OPERATING SYSTEM: ";
    if( _DS_ == '/' ) {
    	$error .= "p4a is configured as running on <b>Linux</b>, if your server operatin system is different, than correct P4A_OS and _DS_ definition.";
    } else {
    	$error .= "p4a is configured as running on <b>Windows</b>, if your server operatin system is different, than correct P4A_OS and _DS_ definition.";
    }
    $error .= "<br>\n";
    
    // DOCUMENT ROOT
    $error .= "Checking DOCUMENT_ROOT: ";
    if( strlen( P4A_SERVER_DIR ) == 0 ) {
    	$error .= "<font color='red'>FAILED</font> (define P4A_SERVER_DIR as your DOCUMENT_ROOT)";
    	$correct = false ;
    } else {
    	$error .= "<font color='green'>OK</font>";
    }
    $error .= "<br>\n";
    
    // UPLOADS DIRECTORY
    $error .= "Checking UPLOADS DIRECTORY: ";
    if( is_readable( P4A_UPLOADS_DIR ) and is_dir( P4A_UPLOADS_DIR ) and is_writable( P4A_UPLOADS_DIR ) ) {
    	$error .= "<font color='green'>OK</font>";
    } else {
    	$error .= "<font color='red'>FAILED</font> (create \"" . P4A_UPLOADS_DIR . "\" and set it writable)";
    	$correct = false ;
    }
    $error .= "<br>\n";
    
    // UPLOADS TEMPORARY DIRECTORY
    $error .= "Checking UPLOADS TEMPORARY DIRECTORY: ";
    if( is_readable( P4A_UPLOADS_TMP_DIR ) and is_dir( P4A_UPLOADS_TMP_DIR ) and is_writable( P4A_UPLOADS_TMP_DIR ) ) {
    	$error .= "<font color='green'>OK</font>";
    } else {
    	$error .= "<font color='red'>FAILED</font> (create \"" . P4A_UPLOADS_TMP_DIR . "\" and set it writable)";
    	$correct = false ;
    }
    $error .= "<br>\n";
    
    // SMARTY COMPILE DIRECTORIES
    $error .= "Checking SMARTY COMPILE DIRECTORIES: ";
    if( is_dir( P4A_SMARTY_MASK_COMPILE_DIR ) and is_writable( P4A_SMARTY_MASK_COMPILE_DIR ) and is_dir( P4A_SMARTY_WIDGET_COMPILE_DIR ) and is_writable( P4A_SMARTY_WIDGET_COMPILE_DIR ) ) {
    	$error .= "<font color='green'>OK</font>";
    } else {
    	$error .= "<font color='red'>FAILED</font> (create \"" . P4A_SMARTY_MASK_COMPILE_DIR . "\" and \"" . P4A_SMARTY_WIDGET_COMPILE_DIR . "\" and set them writable)";
    	$correct = false ;
    }
    $error .= "<br>\n";
    
    // DATABASE CONNECTION
    $error .= "Checking DATABASE CONNECTION: ";
    if( defined( 'P4A_DSN' ) )
    {
    	$db = DB::connect(P4A_DSN);
    	if (DB::isError($db)) {
    		$error .= "<font color='red'>FAILED</font> (check P4A_DSN definition)";
    		$correct = false ;
    	}
    	else
    	{
    		$error .= "<font color='green'>OK</font>";
    	}
    }
    else
    {
    	$error .= "P4A_DSN non defined, no database connection.";
    }
    $error .= "<br>\n";
    
    // REPORT
    $error .= "<h3>Final report:</h3>\n";
    
    if( $correct )
    {
    	$error .= "<font size='+1'>Installation and configuration <font color='green' size='+1'>OK</font>.</font><br>\n";
    	$error .= "<h3>To do:</h3>\n";
    	$error .= "Now you can safely remove configuration_check() from your application.";
    }
    else
    {
    	$error .= "<font size='+1'>Installation and configuration <font color='red'><b>FAILED</b></font>.</font><br>\n";
    	$error .= "<h3>To do:</h3>\n";
    	$error .= "Resolve all the problems to continue execution.<br>\n";
    }
    
    $error = '<html><head><title>p4a Installation and configuration check</title></head><body>' . $error . '</body></html>';
    
    return $correct;
}

?>