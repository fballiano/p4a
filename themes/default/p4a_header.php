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

echo $_xml_header ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title><?php echo P4A::singleton()->getTitle() ?></title>

<?php foreach (P4A::singleton()->getCSS() as $_url=>$_media): ?>
<link href="<?php echo $_url?>" rel="stylesheet" type="text/css" media="<?php echo join(', ', array_keys($_media)) ?>" />
<?php endforeach; ?>

<?php foreach (P4A::singleton()->getJavascript() as $_k=>$_v): ?>
<script type="text/javascript" src="<?php echo $_k ?>"></script>
<?php endforeach; ?>

<?php echo P4A::singleton()->getJavascriptInitializations() ?>
</head>

<body class="p4a_browser_<?php echo P4A::singleton()->getBrowser() ?>">
<div id='p4a_body' class='p4a_browser_<?php echo P4A::singleton()->getBrowserOS() ?>'>
<div id='p4a_loading'><img src='<?php echo P4A_THEME_PATH ?>/loading.gif' alt='' /> <?php echo __('Loading...') ?></div>
<div class='p4a_system_messages'>
	<div class='p4a_system_messages_inner'>
		<?php foreach (P4A::singleton()->getRenderedMessages() as $message): ?>
		<div class='p4a_system_message'><?php echo $message ?></div>
		<?php endforeach; ?>
	</div>
</div>
<?php echo $this->maskOpen() ?>