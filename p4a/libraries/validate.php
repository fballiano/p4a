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

require_once 'Zend/Validate.php';
require_once 'Zend/Validate/Alnum.php';
require_once 'Zend/Validate/Alpha.php';
require_once 'Zend/Validate/Between.php';
require_once 'Zend/Validate/Ccnum.php';
require_once 'Zend/Validate/Date.php';
require_once 'Zend/Validate/Digits.php';
require_once 'Zend/Validate/EmailAddress.php';
require_once 'Zend/Validate/Float.php';
require_once 'Zend/Validate/GreaterThan.php';
require_once 'Zend/Validate/Hex.php';
require_once 'Zend/Validate/Hostname.php';
require_once 'Zend/Validate/InArray.php';
require_once 'Zend/Validate/Int.php';
require_once 'Zend/Validate/Ip.php';
require_once 'Zend/Validate/LessThan.php';
require_once 'Zend/Validate/NotEmpty.php';
require_once 'Zend/Validate/Regex.php';
require_once 'Zend/Validate/StringLength.php';

class P4A_Validate_Alnum extends Zend_Validate_Alnum
{
    /**
     * @param  boolean $allowWhiteSpace
     */
    public function __construct($allowWhiteSpace = false)
    {
    	parent::__construct($allowWhiteSpace);
    	foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_Alpha extends Zend_Validate_Alpha
{
    /**
     * @param  boolean $allowWhiteSpace
     */
    public function __construct($allowWhiteSpace = false)
    {
    	parent::__construct($allowWhiteSpace);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_Between extends Zend_Validate_Between
{
    /**
     * @param  mixed   $min
     * @param  mixed   $max
     * @param  boolean $inclusive
     * @return void
     */
    public function __construct($min, $max, $inclusive = true)
    {
    	parent::__construct($min, $max, $inclusive);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_Ccnum extends Zend_Validate_Ccnum
{
	public function __construct()
	{
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
	}
}

class P4A_Validate_Date extends Zend_Validate_Date
{
	public function __construct()
	{
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
	}
}

class P4A_Validate_Digits extends Zend_Validate_Digits
{
	public function __construct()
	{
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
	}
}

class P4A_Validate_EmailAddress extends Zend_Validate_EmailAddress
{
    /**
     * @param integer                $allow             OPTIONAL
     * @param bool                   $validateMx        OPTIONAL
     * @param Zend_Validate_Hostname $hostnameValidator OPTIONAL
     * @return void
     */
    public function __construct($allow = Zend_Validate_Hostname::ALLOW_DNS, $validateMx = false, Zend_Validate_Hostname $hostnameValidator = null)
    {
    	parent::__construct($allow, $validateMx, $hostnameValidator);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_Float extends Zend_Validate_Float
{
	public function __construct()
	{
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
	}
}

class P4A_Validate_GreaterThan extends Zend_Validate_GreaterThan
{
    /**
     * @param  mixed $min
     */
    public function __construct($min)
    {
    	parent::__construct($min);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_Hex extends Zend_Validate_Hex
{
	public function __construct()
	{
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
	}
}

class P4A_Validate_Hostname extends Zend_Validate_Hostname
{
	/**
     * @param integer          $allow       OPTIONAL Set what types of hostname to allow (default ALLOW_DNS)
     * @param boolean          $validateIdn OPTIONAL Set whether IDN domains are validated (default true)
     * @param boolean          $validateTld OPTIONAL Set whether the TLD element of a hostname is validated (default true)
     * @param Zend_Validate_Ip $ipValidator OPTIONAL
     */
    public function __construct($allow = self::ALLOW_DNS, $validateIdn = true, $validateTld = true, Zend_Validate_Ip $ipValidator = null)
    {
    	parent::__construct($allow, $validateIdn, $validateTld, $ipValidator);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_InArray extends Zend_Validate_InArray
{
    /**
     * @param  array   $haystack
     * @param  boolean $strict
     */
    public function __construct(array $haystack, $strict = false)
    {
    	parent::__construct($haystack, $strict);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_Int extends Zend_Validate_Int
{
	public function __construct()
	{
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
	}
}

class P4A_Validate_Ip extends Zend_Validate_Ip
{
	public function __construct()
	{
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
	}
}

class P4A_Validate_LessThan extends Zend_Validate_LessThan
{
	/**
     * @param  mixed $max
     */
    public function __construct($max)
    {
    	parent::__construct($max);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_NotEmpty extends Zend_Validate_NotEmpty
{
	public function __construct()
	{
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
	}
}

class P4A_Validate_Regex extends Zend_Validate_Regex
{
	/**
     * @param  string $pattern
     */
    public function __construct($pattern)
    {
    	parent::__construct($pattern);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}

class P4A_Validate_StringLength extends Zend_Validate_StringLength
{
	/**
     * @param  integer $min
     * @param  integer $max
     */
    public function __construct($min = 0, $max = null)
    {
    	parent::__construct($min, $max);
        foreach ($this->_messageTemplates as &$messageTemplate) {
    		$messageTemplate = __($messageTemplate);
    	}
    }
}