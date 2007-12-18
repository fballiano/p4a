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
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
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
	 * @access public
	 */
	var $pages = null;

	/**
	 * The name of current page
	 * @var string
	 * @access private
	 */
	var $_active_page = null;

	/**
	 * @param string Object name (identifier)
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->build("P4A_Collection", "pages");
		$this->addAjaxAction("onClick");
		$this->intercept($this, "onClick", "tabClick");
	}

	/**
	 * Builds a new page inside the pane
	 * The new page is a P4A_Frame
	 * @access public
	 */
	function &addPage($page_name, $label=null)
	{
		$this->pages->build('p4a_frame', $page_name);
		if ($label !== null) {
			$this->pages->$page_name->setLabel($label);
		}
		return $this->pages->$page_name;
	}

	/**
	 * Sets the name of currently page open
	 * @access public
	 * @param string, object
	 */
	function setActivePage($page)
	{
		if ($this->actionHandler('beforeSetActivePage') == ABORT) return ABORT;

		switch (gettype($page)) {
			case "string":
				$this->_active_page = $page;
				break;
			case "object":
				$this->_active_page = $page->getName();
				break;
			default:
				P4A_Error('P4A_Tab_pane "' . $this->getName() . '": unable to set "' . gettype($page) . '" as active page, reason: unsopported type');
				break;
		}

		if ($this->actionHandler('afterSetActivePage') == ABORT) return ABORT;
	}

	/**
	 * Returns the name of currently page open
	 * @access public
	 * @return object
	 */
	function &getActivePage()
	{
		if ($this->pages->getNumItems() == 0) {
			$return = null;
			return $return;
		}

		if (strlen($this->_active_page) and
			isset($this->pages->{$this->_active_page}) and
			is_object($this->pages->{$this->_active_page})) {
			return $this->pages->{$this->_active_page};
		}

		$this->pages->reset();
		$page =& $this->pages->nextItem();
		$this->setActivePage($page);
		return $page;
	}

	function getActivePageName()
	{
		$page =& $this->getActivePage();
		if ($page === null) return null;
		return $page->getName();
	}

	function &nextPage()
	{
		if ($this->pages->getNumItems() == 0) {
			$return = null;
			return $return;
		}

		$this->redesign();
		$active_page =& $this->getActivePage();
		$active_page_name = $active_page->getName();

		$this->pages->reset();
		while ($page =& $this->pages->nextItem()) {
			if ($page->getName() == $active_page_name) {
				$page =& $this->pages->nextItem();
				if ($page === null) {
					return $active_page;
				} else {
					$this->setActivePage($page);
				}
			}
		}
	}

	/**
	 * onClick event interceptor
	 * @access private
	 */
	function tabClick($triggering_object, $params)
	{
		$this->setActivePage($params[0]);
		$this->redesign();
	}

	/**
	 * Returns the rendered HTML
	 * @access public
	 */
	function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$active_page =& $this->getActivePage();
		$active_page_name = $active_page->getName();
		$height = $this->getHeight();
		$this->setHeight(null);

		$return  = "<div class='tab_pane' id='$id' " . $this->composeStringProperties() . ">";
		$return .= "<ul class='tabs'>";

		$this->pages->reset();
		while ($page =& $this->pages->nextItem()) {
			if (!$page->isVisible()) continue;
			$actions = $this->composeStringActions($page->getName());
			$active = '';
			if ($page->getName() == $active_page_name) {
				$active = "class='active'";
			}
			if (!strlen($page->getLabel())) {
				$page->setDefaultLabel();
			}
			$label = $page->getLabel();
			$return .= "<li><a href='#' {$actions} {$active}>{$label}</a></li>";
		}
		$return .= "</ul>";
		$return .= "<div class='tab_pane_page' style='height:$height'>" . $active_page->getAsString() . "</div>";
		$return .= "</div>";

		$this->setHeight($height);
		return $return;
	}
}