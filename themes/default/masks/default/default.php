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
?>

<header class="header">
    <span class="logo"><?= \P4A\P4A::singleton()->getTitle() ?></span>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="navbar-btn sidebar-toggle hidden-md hidden-lg" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <p class="navbar-text"><?= $this->getTitle() ?></p>
    </nav>
</header>
<div class="wrapper row-offcanvas row-offcanvas-left">
    <?php if (isset($menu)): ?>
    <aside class="left-side sidebar-offcanvas">
        <section class="sidebar">
            <?= $menu ?>
        </section>
    </aside>
    <?php endif ?>

    <aside class="right-side">
        <section class="content">
            <?php if (isset($top)): ?>
                <div id="p4a_top" class="row">
                    <div class="col-md-12"><?= $top ?></div>
                </div>
            <?php endif; ?>

            <div class='p4a_system_messages'>
                <?php foreach (\P4A\P4A::singleton()->getMessages() as $message): ?>
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 text-center">
                            <div class="alert alert-<?= $message[1] ?> alert-dismissable text-left">
                                <i class="fa fa-<?php
                                switch ($message[1]) {
                                    case "danger":
                                        echo "ban";
                                        break;
                                    case "success":
                                        echo "check";
                                        break;
                                    default:
                                        echo $message[1];
                                }
                                ?>"></i>
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <?= $message[0] ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isset($main)): ?>
                <div id="p4a_main"><?php echo $main ?></div>
            <?php endif; ?>

            <?php if (isset($status_bar)): ?>
                <div id="p4a_statusbar" class="row" style="height:<?php $_statusbar_height = $this->getStatusbarHeight(); echo "{$_statusbar_height[0]}{$_statusbar_height[1]}" ?>;">
                    <?php echo $status_bar; ?>
                </div>
            <?php endif; ?>

            <div class="page-footer">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 text-center">
                        <!-- Removing or modifying the following lines is forbidden and it's a violation of the GNU Lesser General Public License. -->
                        <span class="txt-color-white">Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a> <?php echo P4A_VERSION ?></span>
                    </div>
                </div>
            </div>

        </section>
    </aside>
</div>