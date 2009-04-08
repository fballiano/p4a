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
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */
header('Content-type: text/css');
?>

/*************************/
/* Default mask template */
/*************************/

* {
	font-family: "Bitstream Vera Sans", sans-serif;
	font-size: 11px;
	color: <?php echo $_GET['fg'] ?>;
}

#p4a_top_container {
	width: 100%;
	position: fixed;
	top: 0;
	left: 0;
	z-index: 2;
}

* html #p4a_top_container {
	position: absolute;
}

#p4a_main_container {
	text-align: center;
	padding-top: 5px;
}

#p4a_main_inner_container>* {
	margin-left: auto;
	margin-right: auto;
}

#p4a_footer {
	text-align: center;
	margin-top: 10px;
}

#p4a_body form {
	display: inline;
}

#p4a_body h2,
#p4a_body h2 table td {
	text-align:center;
	font-size: 16px;
	font-weight: bold;
	margin-bottom: 10px;
}

#p4a_loading {
	background: red;
	color: white;
	position: fixed;
	top: 0px;
	right: 0px;
	z-index: 100;
	padding: 5px;
	float: right;
}

* html #p4a_loading {
	position: absolute;
}

#p4a_loading img {
	float: left;
	margin-right: 10px;
}

#p4a_sidebar_left,
#p4a_sidebar_right {
	background: <?php echo $_GET['bg'] ?>;
	border-bottom: <?php echo $_GET['border'] ?>;
	position: fixed !important;
	position: absolute;
 	top: 0;
 	height: 100%;
	padding: 10px;
	z-index: 2;
}

#p4a_sidebar_left {
 	left: 0;
 	border-right: 1px solid <?php echo $_GET['border'] ?>;
}

#p4a_sidebar_right {
	border-left: 1px solid <?php echo $_GET['border'] ?>;
 	right: 0;
}

ol.p4a_backtrace {
	margin-left: 20px;
}

ol.p4a_backtrace li {
	list-style: decimal outside;
}

/***********************/
/* Popup mask template */
/***********************/

#p4a_popup {
	background: <?php echo $_GET['bg'] ?>;
	position: absolute;
	top: 0;
	width: 100%;
	height: 100%;
}

#p4a_popup #p4a_main_container {
	margin-top: 50px;
}

#p4a_popup #p4a_main_inner_container {
	background: white;
	border: 2px solid <?php echo $_GET['selected_bg'] ?>;
	padding-bottom: 20px;
	margin: auto;
}

#p4a_popup h2 {
	margin: 0;
	line-height: 32px;
	padding-left: 32px;
	background: url(gradient.png) bottom repeat-x;
}

* html #p4a_popup h2 {
	margin-right: -100px;
	background-image: none;
}

#p4a_popup #p4a_popup_top_container {
	margin: 0;
	margin-bottom: 20px;
}

#p4a_popup #p4a_main {
	padding: 0 10px;
	padding-right: 5px;
}

* html #p4a_popup #p4a_main {
	padding-left: 0;
	padding-right: 20px;
}

/***********/
/* P4A_Box */
/***********/

.p4a_box {
	text-align: left;
}

.p4a_box strong {
	font-weight: bold;
}

.p4a_box em {
	font-style: italic;
}

/*************/
/* P4A_Frame */
/*************/

.p4a_frame {
	float: left;
}

/****************/
/* P4A_Fieldset */
/****************/

.p4a_fieldset {
	border: 1px solid <?php echo $_GET['input_border'] ?>;
	padding-top: 10px;
	padding-bottom: 10px;
	float: left;
}

.p4a_fieldset .row {
	margin-right: 10px;
}

* html .p4a_fieldset .row {
	margin-left: -10px;
}

.p4a_fieldset legend {
	font-weight: bold;
	font-size: 12px;
	margin-left: 10px;
	color: <?php echo $_GET['fg'] ?>;
	padding: 0 5px;
	text-align: left;
}

/*************/
/* P4A_Field */
/*************/

.p4a_field {
	white-space: nowrap;
}

.p4a_field input,
.p4a_field textarea,
.p4a_field select,
.p4a_field_file table {
	border: 1px solid <?php echo $_GET['input_border'] ?>;
	color: <?php echo $_GET['input_fg'] ?>;
}

.p4a_field option {
	color: <?php echo $_GET['input_fg'] ?>;
}

.p4a_field_label div {
	text-align: left;
	margin-left: 5px;
	clear: both;
}

.p4a_field input {
	width: 150px;
}

.p4a_field_radio input,
.p4a_field_checkbox input {
	border: none;
	width: auto;
}

.p4a_field input:focus,
.p4a_field textarea:focus {
 	border: 1px solid <?php echo $_GET['selected_border'] ?>;
 	outline: 1px solid <?php echo $_GET['selected_border'] ?>;
 	background-image: url(gradient.png);
 	background-position: bottom;
 	background-repeat: repeat-x;
}

.p4a_field_file table {
	margin-left: 1px;
	text-align: center;
	float: left;
	white-space: nowrap;
}

.p4a_field_date input,
.p4a_field_data_integer input,
.p4a_field_data_decimal input,
.p4a_field_data_float input,
.p4a_field_data_date input,
.p4a_field_data_time input
 {
	text-align: right;
}

.field_error input,
.field_error textarea,
.field_error select,
.field_error iframe {
	border: 1px dashed red;
	background: url(<?php echo $_GET['p4a_theme_path'] ?>/widgets/field/error.gif) no-repeat bottom right;
}

.field_error .field_error_msg {
	position: absolute;
	color: <?php echo $_GET['tooltip_fg'] ?>;
	display: none;
	background: <?php echo $_GET['tooltip_bg'] ?>;
	padding: 10px;
	border: 2px solid <?php echo $_GET['tooltip_border'] ?>;
}

.p4a_field_radio_values,
.p4a_field_multicheckbox_values {
	text-align: left;
	float: left;
}

.p4a_field_radio_values input,
.p4a_field_multicheckbox_values input {
	border: none;
}

.p4a_field table.p4a_widget_layout_table {
	display: block;
	float: left;
	clear: both;
}

.p4a_field .p4a_field_date_trigger {
	width: 20px;
	background: <?php echo $_GET['bg'] ?>;
	text-align: center;
}

/****************************/
/* P4A_Field - autocomplete */
/****************************/

.ac_results {
	padding: 0px;
	border: 1px solid <?php echo $_GET['selected_border'] ?>;
	background-color: Window;
	overflow: hidden;
}

.ac_results ul {
	width: 100%;
	list-style-position: outside;
	list-style: none;
	padding: 0;
	margin: 0;
}

.ac_results iframe {
	display:none;/*sorry for IE5*/
	display/**/:block;/*sorry for IE5*/
	position:absolute;
	top:0;
	left:0;
	z-index:-1;
	filter:mask();
	width:3000px;
	height:3000px;
}

.ac_results li {
	margin: 0px;
	padding: 2px 5px;
	cursor: pointer;
	display: block;
	width: 100%;
	font: menu;
	font-size: 11px;
	overflow: hidden;
}

.ac_over {
	background-color: Highlight;
	color: HighlightText;
}

/***************************/
/* P4A_Field - date picker */
/***************************/

.ui-datepicker {
	background: <?php echo $_GET['bg'] ?>;
	border: 2px solid <?php echo $_GET['tooltip_border'] ?>;
}

.ui-datepicker select.ui-datepicker-month,
.ui-datepicker select.ui-datepicker-year {
	background: white;
	color: <?php echo $_GET['fg'] ?>;
	font-weight: normal;
}

/************/
/* P4A_Line */
/************/

hr.p4a_line {
 	margin-top: 10px;
	margin-bottom: 10px;
	border-style: solid;
	width: 1px;
	height: 1px;
}

/********************/
/* P4A_DB_Navigator */
/********************/

.p4a_db_navigator {
	text-align: left;
	font-weight: normal;
	overflow: auto;
}

.p4a_db_navigator li {
	padding: 2px 0 2px 18px;
	background: url(<?php echo $_GET['p4a_icons_path'] ?>/16/places/folder.png) no-repeat;
}

.p4a_db_navigator li.active_node {
	background-image: url(<?php echo $_GET['p4a_icons_path'] ?>/16/status/folder-open.png);
}

.p4a_db_navigator li.home_node {
	background: url(<?php echo $_GET['p4a_icons_path'] ?>/16/actions/go-home.png) no-repeat;
}

.p4a_browser_opera .p4a_db_navigator li,
.p4a_browser_ie .p4a_db_navigator li {
	margin-top: 5px;
	padding-top: 0;
}

* html .p4a_browser_ie .p4a_db_navigator li {
	padding: top: 2px;
	margin-top: 0;
	width: 100%;
}

.p4a_db_navigator a,
.p4a_db_navigator {
	text-decoration: none;
}

.p4a_db_navigator a:hover {
	text-decoration: underline;
}

.p4a_db_navigator .active_node {
	font-weight: bold;
}

.p4a_db_navigator .hoverclass {
	color: <?php echo $_GET['selected_fg'] ?>;
	background: <?php echo $_GET['selected_bg'] ?>;
}

.p4a_db_navigator .hoverclass li a {
	color: <?php echo $_GET['fg'] ?>;
}

/***************************/
/* P4A_Widget_Layout_Table */
/***************************/

table.p4a_widget_layout_table {
	border-collapse: collapse;
	margin: 0 auto;
	text-align: left;
}

table.p4a_widget_layout_table td {
	vertical-align: middle;
	margin: 0;
	padding: 0;
}

table.p4a_widget_layout_table td.c1 {
	width: 1px;
	padding-right: 5px;
}

/***********************************/
/* P4A_Message and system messages */
/***********************************/

.p4a_system_messages {
	z-index: 10000;
	position: absolute;
	display: none;
	width: 300px;
	overflow: hidden;
}

.p4a_system_messages_inner {
	color: <?php echo $_GET['tooltip_fg'] ?>;
	background: <?php echo $_GET['tooltip_bg'] ?>;
	border: 2px solid <?php echo $_GET['tooltip_border'] ?>;
}

.p4a_system_messages table.p4a_message {
	margin: 10px 20px;
}

.p4a_system_messages em {
	font-style: italic;
}

/**************/
/* P4A_Button */
/**************/

.p4a_button {
	background: <?php echo $_GET['bg'] ?>;
	border: 1px solid <?php echo $_GET['border'] ?>;
	cursor: pointer;
	overflow: visible; /* used to fix the IE stretched buttons bug */
}

.p4a_button_image {
	border: 1px solid <?php echo $_GET['bg'] ?>;
	padding: 0;
}

.p4a_button:hover {
	border: 1px solid <?php echo $_GET['selected_bg'] ?>;
	outline: 1px solid <?php echo $_GET['selected_bg'] ?>;
}

.p4a_button_disabled,
.p4a_button_image_disabled {
	opacity: 0.2;
	-moz-opacity: 0.2;
	filter: alpha(opacity=20);
	cursor: default;
}

.p4a_button_disabled:hover {
	color: <?php echo $_GET['fg'] ?>;
	background: <?php echo $_GET['bg'] ?>;
	border: 1px solid <?php echo $_GET['border'] ?>;
}

/***************/
/* P4A_Toolbar */
/***************/

.p4a_toolbar {
	background: <?php echo $_GET['bg'] ?> url(gradient.png) bottom repeat-x;
	border-bottom: 1px solid <?php echo $_GET['border'] ?>;
	padding: 2px;
}

#p4a_popup .p4a_toolbar {
	border: none;
}

* html .p4a_toolbar {
	background-image: none;
}

.p4a_toolbar .p4a_button {
	background: none;
	border: 1px solid transparent;
}

* html .p4a_toolbar .p4a_button {
	border: 1px solid <?php echo $_GET['bg'] ?>;
}

.p4a_toolbar .p4a_button_image:hover {
	border: 1px solid <?php echo $_GET['border'] ?>;
	outline: none;
}

.p4a_toolbar_16 button {
	height: 21px;
}

.p4a_toolbar_32 button {
	height: 37px;
}

.p4a_toolbar_48 button {
	height: 53px;
}

.p4a_toolbar_separator {
	border-left: 1px solid <?php echo $_GET['border'] ?>;
	margin: 3px 5px 0 5px;
}

/*************/
/* P4A_Label */
/*************/

.p4a_label {
	overflow: visible;
	display: block;
	text-align: left;
	cursor: pointer;
	float: left;
	width: 100px;
	white-space: normal;
}

.p4a_label_required {
	font-weight: bold;
}

.p4a_label .p4a_tooltip_icon {
	float: right;
}

.p4a_tooltip {
	display: none;
	position: absolute;
	margin-left: 20px;
	z-index: 10000;
}

.p4a_tooltip_inner {
	padding: 10px;
	text-align: left;
	font-weight: normal;
	color: <?php echo $_GET['tooltip_fg'] ?>;
	background: <?php echo $_GET['tooltip_bg'] ?>;
	border: 2px solid <?php echo $_GET['tooltip_border'] ?>;
}

/****************/
/* P4A_Tab_Pane */
/****************/

.p4a_tab_pane ul.tabs {
	border-bottom: 1px solid <?php echo $_GET['input_border'] ?>;
	padding-left: 20px;
	text-align: left;
}

.p4a_browser_ie .p4a_tab_pane ul.tabs {
	padding-top: 1px;
}

.p4a_browser_linux .p4a_tab_pane ul.tabs {
	padding-bottom: 1px;
}

.p4a_tab_pane ul.tabs li {
	display: inline;
}

.p4a_tab_pane ul.tabs li a {
	padding: 0 5px;
	text-decoration: none;
	border: 1px solid <?php echo $_GET['input_border'] ?>;
	background: #fafafa;
}

.p4a_tab_pane ul.tabs a.active,
.p4a_tab_pane ul.tabs a.active:hover {
	background: <?php echo $_GET['selected_bg'] ?>;
	border-bottom: 1px solid white;
	cursor: default;
	margin-right: -1px;
}

.p4a_tab_pane ul.tabs a:hover {
	background: <?php echo $_GET['selected_bg'] ?>;
}

.p4a_tab_pane div.p4a_tab_pane_page {
	border: 1px solid <?php echo $_GET['input_border'] ?>;
	border-top: none;
	padding: 10px 10px 10px 0;
	overflow: hidden;
}

/*************/
/* P4A_Table */
/*************/

.p4a_table {
	border: 1px solid <?php echo $_GET['input_border'] ?>;
	text-align: left;
}

.p4a_table caption {
	background: <?php echo $_GET['bg'] ?> url(gradient.png) bottom repeat-x;
	border: 1px solid <?php echo $_GET['input_border'] ?>;
	padding: 5px;
	font-weight: bold;
}

* html .p4a_table caption {
	background-image: none;
}

.p4a_browser_gecko .p4a_table caption {
	margin-left: -1px;
}

.p4a_browser_safari .p4a_table caption {
	margin-right: -1px;
	border-bottom: none;
}

.p4a_browser_opera .p4a_table caption {
	border-bottom: none;
}

.p4a_table td,
.p4a_table th {
	border-right: 1px solid <?php echo $_GET['input_border'] ?>;
}

.p4a_table th {
	padding: 5px;
	font-weight: bold;
	background: <?php echo $_GET['bg'] ?> url(gradient.png) bottom repeat-x;
	border-bottom: 1px solid <?php echo $_GET['input_border'] ?>;
}

* html .p4a_table th {
	background-image: none;
}

.p4a_table th.p4a_row_indicator {
	border-bottom: none;
	background: white;
	width: 15px;
}

.p4a_table td {
	padding: 2px;
}

.p4a_table th a,
.p4a_table td a {
	display: block;
	text-decoration: none;
}

.p4a_table td a:hover {
	text-decoration: underline;
}

.p4a_table td.integer,
.p4a_table td.decimal,
.p4a_table td.float,
.p4a_table td.date,
.p4a_table td.time,
.p4a_table td.integer textarea,
.p4a_table td.decimal textarea,
.p4a_table td.float textarea,
.p4a_table td.date textarea,
.p4a_table td.time textarea {
	text-align: right;
	white-space: nowrap;
}

.p4a_table td.file {
	text-align: center;
}

.p4a_table td.action {
	background: <?php echo $_GET['bg'] ?>;
}

.p4a_table td.action a img {
	vertical-align: middle;
}

.p4a_table_rows1 {
	background-color: <?php echo $_GET['even_row'] ?>;
}

.p4a_table_rows2 {
	background-color: <?php echo $_GET['odd_row'] ?>;
}

.p4a_table th.p4a_toolbar {
	font-weight: normal;
	border-top: 1px solid <?php echo $_GET['input_border'] ?>;
	padding: 0;
}

.p4a_table th.p4a_toolbar .p4a_box,
.p4a_table th.p4a_toolbar .p4a_field {
	margin-top: 3px;
}

.p4a_table_navigation_bar .p4a_label {
	width: auto;
	margin-right: 5px;
}

/************/
/* P4A_Grid */
/************/

.p4a_grid_td_disabled {
	background: <?php echo $_GET['bg'] ?>;
}

.p4a_grid_td {
	border: 1px solid <?php echo $_GET['input_border'] ?>;
	border-collapse: collapse;
	margin: 0;
	height:25px;
}

.p4a_grid_text {
	border: none;
	margin: 0;
	padding: 0;
}

.p4a_grid_text:focus {
 	background-image: url(gradient.png);
 	background-position: bottom;
 	background-repeat: repeat-x;
 	border: 1px solid <?php echo $_GET['selected_border'] ?>;
 	outline: 1px solid <?php echo $_GET['selected_border'] ?>;
}

/************/
/* P4A_Menu */
/************/

#p4a_menu {
	background: <?php echo $_GET['bg'] ?> url(gradient.png) bottom repeat-x;
	border-bottom: 1px solid <?php echo $_GET['border'] ?>;
}

* html #p4a_menu {
	background-image: none;
}

.p4a_menu {
	list-style-type: none;
	margin: 0;
	padding: 0;
	position: static;
	z-index: 20;
}

.p4a_menu table.p4a_widget_layout_table {
	margin: 0;
}

.p4a_menu table.p4a_widget_layout_table td.c1 img {
	margin-left: 5px;
}

.p4a_menu li {
	float: left;
	display: block;
}

.p4a_menu ul {
	position: absolute;
	margin: 0;
	padding: 0;
	list-style-type: none;
	display: none;
	background-color: <?php echo $_GET['bg'] ?>;
	z-index: 40;
	border: 1px solid #ccc;
	width: 120px;
}

* html .p4a_menu ul {
	margin-top: -5px;
}

.p4a_menu ul li {
	width: 120px;
	margin: 0;
}

.p4a_menu li a,
.p4a_menu li div {
	display: block;
	text-decoration: none;
	color: <?php echo $_GET['fg'] ?>;
	margin: 0;
	padding: 5px;
}

.p4a_menu li:hover {
	display: block;
	background: <?php echo $_GET['selected_bg'] ?> url(gradient.png) bottom repeat-x;
}

.p4a_menu li:hover>a,
.p4a_menu li:hover>a>span,
.p4a_menu li:hover>div,
.p4a_menu li:hover>div>span {
	color: <?php echo $_GET['selected_fg'] ?>;
}

.p4a_menu ul ul {
	display: none;
	margin-top: -2em;
	margin-left: 120px;
}

.p4a_menu li:hover ul ul,
.p4a_menu li:hover ul ul ul,
.p4a_menu li:hover ul ul ul ul,
.p4a_menu li:hover ul ul ul ul ul {
	display: none;
}

.p4a_menu li:hover ul,
.p4a_menu ul li:hover ul,
.p4a_menu ul ul li:hover ul,
.p4a_menu ul ul ul li:hover ul,
.p4a_menu ul ul ul ul li:hover ul {
	display: block;
}

/***********/
/* Various */
/***********/

.accesskey {
	text-decoration: underline;
}

.hidden {
	display: none;
}

.row,
.br {
	clear: both;
}

.p4a_shadow {
	padding: 0 6px 6px 0;
}

.p4a_shadow_r {
	position: absolute;
	top: 0;
	right: 0;
	bottom: 6px;
	width: 6px;
	background: url(shadow-r.png) top;
}

.p4a_shadow_b {
	position: absolute;
	bottom: 0;
	left: 0;
	right: 6px;
	height: 6px;
	background: url(shadow-b.png) left;
}

.p4a_shadow_br {
	position: absolute;
	bottom: 0;
	right: 0;
	width: 6px;
	height: 6px;
	background: url(shadow-br.png);
}