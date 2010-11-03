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

/**
 * The tab pane widget
 * A tab pane is a set of pages.
 * Only one page is visible at time.
 * The pages are switchable from a tabbed menu.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class P4A_Tab_Pane extends P4A_Widget
{
   	/**
	 * @var P4A_Collection
	 */
	public $pages = null;

	/**
	 * The name of current page
	 * @var string
	 */
	protected $_active_page_name = null;

	/**
	 * @param string $name Object name (identifier)
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->build("P4A_Collection", "pages");
		$this->addAjaxAction("onclick");
		$this->intercept($this, "onclick", "tabClick");
	}

	/**
	 * Builds a new page inside the pane
	 * @return P4A_Frame
	 */
	public function addPage($page_name, $label=null)
	{
		$this->pages->build('p4a_frame', $page_name);
		if ($label !== null) {
			$this->pages->$page_name->setLabel($label);
		}
		return $this->pages->$page_name;
	}

	/**
	 * @param string|P4A_Frame $page
	 * @return P4A_Frame
	 */
	public function setActivePage($page)
	{
		if ($this->actionHandler('beforeSetActivePage') == ABORT) return ABORT;

		switch (gettype($page)) {
			case "string":
				$this->_active_page_name = $page;
				break;
			case "object":
				$this->_active_page_name = $page->getName();
				break;
			default:
				trigger_error('P4A_Tab_pane "' . $this->getName() . '": unable to set "' . gettype($page) . '" as active page, reason: unsopported type', E_USER_ERROR);
				break;
		}

		if ($this->actionHandler('afterSetActivePage') == ABORT) return ABORT;
		return $this->getActivePage();
	}

	/**
	 * @return P4A_Frame
	 */
	public function getActivePage()
	{
		if ($this->pages->getNumItems() == 0) {
			$return = null;
			return $return;
		}

		if (strlen($this->_active_page_name) and
			isset($this->pages->{$this->_active_page_name}) and
			is_object($this->pages->{$this->_active_page_name})) {
			return $this->pages->{$this->_active_page_name};
		}

		$this->pages->reset();
		$page = $this->pages->nextItem();
		$this->setActivePage($page);
		return $page;
	}

	/**
	 * @return string
	 */
	public function getActivePageName()
	{
		if (!$this->_active_page_name) {
			$this->_active_page_name = $this->getActivePage()->getName();
		}
		return $this->_active_page_name;
	}

	/**
	 * @return P4A_Frame
	 */
	public function nextPage()
	{
		if ($this->pages->getNumItems() == 0) {
			$return = null;
			return $return;
		}

		$this->redesign();
		$active_page_name = $this->getActivePage()->getName();

		$this->pages->reset();
		while ($page = $this->pages->nextItem()) {
			if ($page->getName() == $active_page_name) {
				$page = $this->pages->nextItem();
				if ($page === null) {
					return $active_page;
				} else {
					return $this->setActivePage($page);
				}
			}
		}
	}

	/**
	 * onClick event interceptor
	 * @param P4A_Object $triggering_object
	 * @param array $params
	 */
	public function tabClick($triggering_object, $params)
	{
		$this->setActivePage($params[0]);
		$this->redesign();
	}

	/**
	 * Returns the rendered HTML
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$height = $this->getHeight();
		$this->setHeight(null);

		$class = $this->composeStringClass(array("ui-tabs", "ui-widget", "ui-widget-content"));
		$properties = $this->composeStringProperties();
		$return  = "<div id='$id' $class $properties>";
		$return .= "<ul class='tabs ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>";

		$active_page_name = $this->getActivePageName();
		$this->pages->reset();
		while ($page = $this->pages->nextItem()) {
			if (!$page->isVisible()) continue;
			$actions = $page->isEnabled() ? $this->composeStringActions($page->getName()) : "";
			$disabled = $page->isEnabled() ? "" : "disabled";
			$active = "";
			if ($page->getName() == $active_page_name) {
				$active = "ui-tabs-selected ui-state-active";
			}
			if (!strlen($page->getLabel())) {
				$page->setDefaultLabel();
			}
			$label = __($page->getLabel());
			$tooltip = __($page->getTooltip());
			$mouseover = "";
			if (strlen($tooltip)) {
				$tooltip = "<div id='{$page->getId()}tooltip' class='p4a_tooltip'><div class='p4a_tooltip_inner'>$tooltip</div></div>";
				$mouseover = "onmouseover='p4a_tooltip_show(this)'";
				$label .= ' <img src="' . P4A_ICONS_PATH . '/16/status/dialog-information.png" class="p4a_tooltip_icon" alt="" />';
			}
			$return .= "<li class='ui-state-default ui-corner-top $active $disabled' $mouseover><a href='#' $actions>$label</a>$tooltip</li>";
		}
		$return .= "</ul>";
		$return .= "<div class='p4a_tab_pane_page ui-tabs-panel ui-widget-content ui-corner-all ui-helper-clearfix' style='height:$height'>" . $this->getActivePage()->getAsString() . "</div>";
		$return .= "</div>";
		$return .= "<script type='text/javascript'>p4a_tabs_load();</script>";

		$this->setHeight($height);
		return $return;
	}
}