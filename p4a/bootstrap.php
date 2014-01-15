<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with P4A.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 *
 * To contact the authors write to:                                     <br />
 * Fabrizio Balliano <fabrizio@fabrizioballiano.it>                     <br />
 * Andrea Giardina <andrea.giardina@crealabs.it>
 *
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

namespace P4A;

require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/constants.php";
require_once __DIR__ . "/pear_net_useragent_detect.php";

ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . __DIR__);
require_once __DIR__ . "/Zend/Loader/StandardAutoloader.php";
$loader = new \Zend_Loader_StandardAutoloader(array(
    'prefixes' => array(
        'Zend' => __DIR__ . "/Zend",
    ),
    'namespaces' => array(
        'P4A' => __DIR__ . "/P4A",
    )
));
$loader->register();

/*
require_once __DIR__ . "/P4A/Autoload.php";

Autoload::singleton()
    ->addNamespace("P4A", __DIR__ . DIRECTORY_SEPARATOR . "P4A")
    ->addNamespace("Zend", __DIR__ . DIRECTORY_SEPARATOR . "Zend");
*/