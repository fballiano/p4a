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
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="<?php echo \P4A\P4A::singleton()->getMetaViewport() ?>">
<title><?= \P4A\P4A::singleton()->getTitle() ?></title>

<?php foreach (\P4A\P4A::singleton()->getCSS() as $_url=>$_media): ?>
<link href="<?php echo $_url?>" rel="stylesheet" type="text/css" media="<?php echo join(', ', array_keys($_media)) ?>" />
<?php endforeach; ?>

<?php foreach (\P4A\P4A::singleton()->getJavascript() as $_k=>$_v): ?>
<script type="text/javascript" src="<?php echo $_k ?>"></script>
<?php endforeach; ?>

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->

<?php echo \P4A\P4A::singleton()->getJavascriptInitializations() ?>
</head>

<body class="skin-blue" id="p4a_body">
<div id='p4a_body'>
<div id='p4a_loading' style="position:absolute;top:0;right:0;z-index:1000;background:red;white-space:nowrap;color:white;padding:5px">
	<img src='<?php echo P4A_THEME_PATH ?>/loading.gif' alt='' /> <?php echo __('Loading...') ?>
	<div id='p4a_loading_percentage'></div>
</div>
<?php echo $this->maskOpen() ?>