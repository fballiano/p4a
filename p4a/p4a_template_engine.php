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
 * To contact the authors write to:								<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)											<br>
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
 * The template engine for P4A.
 * This class manages the template engine that P4A uses to render some widgets.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Template_Engine
{
	/**
	 * Retrieve a singleton instance of the P4A template engine.
	 * @access public
	 */
	function &singleton()
  	{
		static $template_engine;
		if(!isset($template_engine)) {
			$options = array();
			$options["compileDir"] = P4A_COMPILE_DIR;
			$options["templateDir"] = P4A_THEME_DIR;
			$options["allowPHP"] = true;
			$options["globals"] = true;
			$options["globalfunctions"] = true;
			$template_engine =& new HTML_Template_Flexy($options);
		}
		return $template_engine;
	}

	/**
	 * Renders a template and returns the HTML.
	 * @param object		The object that contain all the variables that the template should display.
	 * @param string		The path (with filename and extension) of the template (relative to P4A_THEME_DIR)
	 * @access public
	 */
	function getAsString(&$object, $file_relative_path)
	{
		$engine =& P4A_Template_Engine::singleton();
		$engine->compile($file_relative_path);
		return $engine->bufferedOutputObject($object);
	}
}