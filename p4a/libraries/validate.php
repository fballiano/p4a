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

require_once dirname(__FILE__) . "/pear/Validate.php";

/**
 * Data validation class
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Validate
{
	/**
	 * @access private
	 * @param mixed		(P4A_Field|P4A_Data_Field|string)
	 * @return string
	 */
	function _getData($data)
	{
		if (is_object($data)) {
			if (is_a($data, 'P4A_Field')) {
				return $data->data_field->getNewValue();
			} elseif (is_a($data, 'P4A_Data_Field')) {
				return $data->getNewValue();
			} else {
				P4A_Error("P4A_Validate: only p4a_field or p4a_data_field are allowed");
			}
		}
		return $data;
	}

	/**
	 * @access public
	 * @param mixed		(P4A_Field|P4A_Data_Field|string)
	 * @return boolean
	 */
	function notEmpty($data)
	{
		$data = P4A_Validate::_getData($data);
		return strlen($data) > 0;
	}

	/**
	 * @access public
	 * @param mixed		(P4A_Field|P4A_Data_Field|string)
	 * @param array		format: array(day, month, year) eg: array(30, 10, 2006)
	 * @param array		format: array(day, month, year) eg: array(30, 10, 2006)
	 * @param string	strftime compatibile format string
	 * @return boolean
	 */
	function date($data, $min=null, $max=null, $format='%Y-%m-%d')
	{
		$data = P4A_Validate::_getData($data);

		$options = array();
		if ($min !== null) $options['min'] = $min;
		if ($max !== null) $options['max'] = $max;
		$options['format'] = $format;

		return Validate::date($data, $options);
	}

	/**
	 * @access public
	 * @param mixed		(P4A_Field|P4A_Data_Field|string)
	 * @param boolean	check if the domain exists
	 * @param boolean
	 * @return boolean
	 */
	function email($data, $check_domain=false, $use_rfc822=false)
	{
		$data = P4A_Validate::_getData($data);

		$options = array();
		$options['check_domain'] = $check_domain;
		$options['use_rfc822'] = $use_rfc822;

		return Validate::email($data, $options);
	}

	/**
	 * @access public
	 * @param mixed		(P4A_Field|P4A_Data_Field|string)
	 * @param integer
	 * @param integer
	 * @param integer	0 means no decimals
	 * @param string
	 * @return boolean
	 */
	function number($data, $min=null, $max=null, $decimal_precision=null, $decimal_separator='.')
	{
		$data = P4A_Validate::_getData($data);

		$options = array();
		if ($min !== null) $options['min'] = $min;
		if ($max !== null) $options['max'] = $max;
		if ($decimal_precision !== null) {
			if ($decimal_precision>0) {
				$options['decimal'] = $decimal_separator;
				$options['dec_prec'] = $decimal_precision;
			} else {
				$options['decimal'] = false;
			}
		}

		return Validate::number($data, $options);
	}

	/**
	 * @access public
	 * @param mixed		(P4A_Field|P4A_Data_Field|string)
	 * @param integer
	 * @param integer
	 * @param string	Perl regular expression pattern
	 * @return boolean
	 */
	function string($data, $min_length=null, $max_length=null, $format=null)
	{
		$data = P4A_Validate::_getData($data);

		$options = array();
		if ($min_length !== null) $options['min_length'] = $min_length;
		if ($max_length !== null) $options['max_length'] = $max_length;
		if ($format !== null) $options['format'] = $format;

		return Validate::string($data, $options);
	}

	/**
	 * @access public
	 * @param mixed		(P4A_Field|P4A_Data_Field|string)
	 * @param array		eg: array('http', 'ftp')
	 * @param boolean	check if the domain exists
	 * @param string
	 * @return boolean
	 */
	function uri($data, $allowed_schemes=null, $check_domain=false, $forbidden_chars=';/?:@$,')
	{
		$data = P4A_Validate::_getData($data);

		$options = array();
		if ($allowed_schemes !== null) $options['allowed_schemes'] = $allowed_schemes;
		$options['domain_check'] = $check_domain;
		$options['strict'] = $forbidden_chars;

		return Validate::uri($data, $options);
	}
}