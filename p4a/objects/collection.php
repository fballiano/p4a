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
 * To contact the authors write to:                                 <br>
 * CreaLabs                                                         <br>
 * Via Medail, 32                                                   <br>
 * 10144 Torino (Italy)                                             <br>
 * Web:    {@link http://www.crealabs.it}                           <br>
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

class P4A_Collection extends P4A_Object
{
    var $_pointer = 0;

    //todo: in caso di destroy di un elemento fare l'unset della chiave e ridurre l'array
    function &nextItem()
    {
        $p4a =& P4A::singleton();
        if ($this->_pointer < $this->getNumItems()) {
            $id = $this->_objects[$this->_pointer];
            $this->_pointer++;
            if (!isset($p4a->objects[$id]) or !is_object($p4a->objects[$id])) {
            	$this->_pointer--;
            	unset($this->_objects[$this->_pointer]);
            	$this->_objects = array_values($this->_objects);
            	return $this->nextItem();
            } else {
            	return $p4a->objects[$id];
            }
        } else {
            $this->_pointer = 0;
            $ret = null; //php 4.4 fix
            return $ret;
        }
    }

    //todo
    function getNumItems()
    {
        return count($this->_objects);
    }

    function reset()
    {
        $this->_pointer = 0;
    }

    function getNames()
    {
        $names = array();

        while ($item =& $this->nextItem()) {
            $names[] = $item->getName();
        }

        return $names;
    }
}