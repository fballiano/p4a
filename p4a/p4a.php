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

	/**
	 * The main p4a file.
	 */
	 
	//Setting PHP error reporting
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	error_reporting(E_ALL);
	
	//Disabling magic_quotes_runtime (default)
	ini_set('magic_quotes_runtime','Off');
	ini_set('magic_quotes_sybase','Off');
	 
	//Main inclusion file
	require_once(dirname(__FILE__) . '/include.php');
	
	//We can have more projects on same site and same browser instance
	session_name('sn_' . preg_replace('~\W~', '_', P4A_PROJECT_NAME) . '_p4a');
	
	$action_return = NULL;
	
	//We're going to instance new application if it's not already instanced.
	session_id(session_id() . 'p4a');
	session_start();
// 	if ( !array_key_exists( 'p4a', $_SESSION ) )
// 	{
// 		$p4a = new P4A();
// 
// 		$action_handler = new global_listener();
// 		$action_handler->p4a =& $p4a ;
// 		$p4a->listener =& $action_handler; 
// 		$action_handler->intercept($p4a, 'init', 'init');
// 		$action_handler->intercept($p4a, 'main', 'main');
// 		$p4a->init();
// 
// 		$_SESSION['p4a'] = $p4a;
// 	}
// 	else
// 	{
// 		$p4a = $_SESSION['p4a'];
// 		
// 		// This is not so nice but it seems that 
// 		// PHP session module desn't support referenced ojbect.
// 		$p4a =& $p4a->listener->p4a;
// 	}
// 	
// 	$p4a->main();
		
?>
