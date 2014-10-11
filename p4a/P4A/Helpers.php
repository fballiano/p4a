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

/**
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
abstract class Helpers
{
    static function P4A_Widget_Field_loadSelectByArray($field, $params)
    {
        $field->build('P4A\\DataSource\\ArraySource', 'source');
        if (isset($params[1])) {
            $field->source->setPk($params[1]);
        }
        $field->source->load($params[0]);

        $field->setType('select');
        $field->setSource($field->source);
    }

    static function P4A_Widget_Field_loadSelectByTable($field, $params)
    {
        list($table, $pk, $description_field) = $params;
        if (!$description_field) {
            $description_field = $pk;
        }

        $field->build('P4A\\DataSource\\DbSource', 'source');
        $field->source->setTable($table);
        $field->source->setPK($pk);
        $field->source->addOrder($description_field);
        $field->source->load();

        $field->setType('select');
        $field->setSource($field->source);
        $field->setSourceDescriptionField($description_field);
    }

    static function P4A_Widget_Frame_anchorTabPane($obj, $params)
    {
        $tab_pane_name = $params[0];
        $tab_pane = $obj->build('P4A\\Widget\\TabPane', $tab_pane_name);

        for ($i = 1; $i < count($params); $i++) {
            $tab_pane->addPage($params[$i]);
        }

        $obj->anchor($tab_pane);
        return $tab_pane;
    }

    static function P4A_Mask_Mask_constructSimpleEdit($mask, $params)
    {
        list($source) = $params;

        // source
        if (is_string($source)) {
            $mask->build('P4A\\DataSource\\DbSource', 'source')
                ->setTable($source)
                ->load();
        } elseif (is_a($source, 'P4A\\DataSource\\DbSource')) {
            $table = $source->getTable();
            if (strlen($table) == 0) {
                trigger_error("The passed P4A_DB_Source has no master table", E_USER_ERROR);
            }
            $mask->source = $source;
        } else {
            trigger_error(
                "You did not pass a valid \"source\" param, please pass a string or a P4A_DB_Source",
                E_USER_ERROR
            );
        }
        $mask->setSource($mask->source);

        // toolbar
        $mask->build('P4A\\Widget\\Toolbar\\Full', 'toolbar')
            ->setMask($mask);

        // table
        $mask->build('P4A\\Widget\\Table', 'table')
            ->setSource($mask->source)
            ->setWidth(500)
            ->showNavigationBar();

        // fieldset with anchored objects
        $mask->build('P4A\\Widget\\Fieldset', 'fieldset');
        while ($field = $mask->fields->nextItem()) {
            $mask->fieldset->anchor($field);
        }

        // main frame
        $mask->frame
            ->anchor($mask->table)
            ->anchor($mask->fieldset);

        // last things
        $mask
            ->display("top", $mask->toolbar)
            ->setFocus($mask->fields->nextItem())
            ->firstRow();

        // resetting fields collection pointer
        $mask->fields->reset();
    }

    static function P4A_Mask_Mask_setTableAsSource($mask, $params)
    {
        list($table, $pk) = $params;
        $mask->build('P4A\\DataSource\\DbSource', 'source');
        $mask->source->setTable($table);
        $mask->source->setPK($pk);
        $mask->source->load();
        $mask->source->firstRow();

        $mask->setSource($mask->source);
    }

    static function P4A_Mask_Mask_useToolbar($obj, $params)
    {
        $toolbar = $params[0];
        $obj->build("P4A\\Widget\\Toolbar\\{$toolbar}", 'toolbar');
        $obj->toolbar->setMask($obj);
        $obj->display('top', $obj->toolbar);
    }

    static function P4A_Widget_Table_addDeleteRow($table, $params)
    {
        if (isset($params[0])) {
            $col_label = $params[0];
        } else {
            $col_label = 'Delete';
        }

        if (isset($params[1])) {
            $message = $params[1];
        } else {
            $message = 'Delete current element';
        }

        $table->addActionCol('delete');
        $table->cols->delete->setWidth(150)
            ->setLabel($col_label)
            ->requireConfirmation('onClick', $message);
        $table->data->intercept($table->cols->delete, 'afterClick', 'deleteRow');

        return $table;
    }
}