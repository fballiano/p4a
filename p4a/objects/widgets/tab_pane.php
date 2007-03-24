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
 * A tab pane is a collection of widgets.
 * Only one page is visible at time.
 * The pages are switchable from a tabbed menu.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Tab_Pane extends P4A_Widget
{
   	/**
	 * The pages collection
	 * @var array
	 * @access public
	 */
	var $pages = null;

	/**
	 * The name of currently page open
	 * @var string
	 * @access private
	 */
	var $_active_page = null;

	/**
	 * Tab Pane constructor.
	 * @param string Object name (identifier).
	 */
	function P4A_Tab_Pane($name)
	{
		parent::P4A_Widget($name);
		$this->useTemplate('tab_pane');
		$this->build("P4A_Collection", "pages");
		$this->addAjaxAction("onClick");
		$this->intercept($this, "onClick", "tabClick");
	}

	/**
	 * Builds a new page inside the pane
	 * The new built page is a P4A_Frame
	 * @access public
	 */
	function addPage($page_name, $label=NULL)
	{
		$this->pages->build('p4a_frame', $page_name);
		if ($label !== NULL) {
			$this->pages->$page_name->setLabel($label);
		}
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
				P4A_Error('Unsupported page type for P4A_Tab_Pane');
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
		$return = null;

		if ($this->pages->getNumItems() > 0) {
			if (isset($this->_active_page)) {
				if (is_object($this->pages->{$this->_active_page})) {
					$return = $this->pages->{$this->_active_page};
				}
			} else {
				$this->pages->reset();
				$return = $this->pages->nextItem();
			}
		}

		return $return;
	}

	function tabClick($page, $params)
	{
		$this->setActivePage($params[0]);
		$this->redesign();
	}

	/**
	 * Returns the HTML rendered
	 * @access public
	 */
	function getAsString()
	{
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$active_page =& $this->getActivePage();
		$active_page_name = $active_page->getName();

		// saving height and emptying it
		// because we've to write it in the inner div
		$tmpHeight = $this->getHeight();
		$this->setHeight(null);

		// re-setting height
		if ($tmpHeight) {
			$this->setHeight($tmpHeight);
			$this->addTempVar('tab_pane_height', "height:{$tmpHeight}");
		}

		$tabs = array();
		$i = 0;
		$this->pages->reset();
		while ($page =& $this->pages->nextItem()) {
			if (!$page->isVisible()) {
				continue;
			}

			$active = false;
			$page_name = $page->getName();
			if ($page_name == $active_page_name	) {
				$active = true;
			}

			$actions = $this->composeStringActions($page_name);
			if (!$page->getLabel()) {
				$page->setDefaultLabel();
			}

			$tabs[$i]['actions'] =  $actions;
			$tabs[$i]['active']  =  $active;
			$tabs[$i]['label']   =  $page->getLabel();
			$i++;
		}
		$this->addTempVar('tabs', $tabs);

		if ($active_page->isVisible()) {
			$this->addTempVar('active_page', $active_page->getAsString());
		}

		return $this->fetchTemplate();
	}

}