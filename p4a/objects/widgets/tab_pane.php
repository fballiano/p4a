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

/**
 * The tab pane widget
 * A tab pane is a set of pages.
 * Only one page is visible at time.
 * The pages are switchable from a tabbed menu.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
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
				P4A_Error('P4A_Tab_pane "' . $this->getName() . '": unable to set "' . gettype($page) . '" as active page, reason: unsopported type');
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

		$class = $this->composeStringClass();
		$properties = $this->composeStringProperties();
		$return  = "<div id='$id' $class $properties>";
		$return .= "<ul class='tabs'>";

		$this->pages->reset();
		while ($page = $this->pages->nextItem()) {
			if (!$page->isVisible()) continue;
			$actions = $this->composeStringActions($page->getName());
			$active = '';
			if ($page->getName() == $this->_active_page_name) {
				$active = "class='active'";
			}
			if (!strlen($page->getLabel())) {
				$page->setDefaultLabel();
			}
			$label = $page->getLabel();
			$return .= "<li><a href='#' $actions $active>$label</a></li>";
		}
		$return .= "</ul>";
		$return .= "<div class='p4a_tab_pane_page' style='height:$height'>" . $this->getActivePage()->getAsString() . "</div>";
		$return .= "</div>";

		$this->setHeight($height);
		return $return;
	}
}