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
 * The canvas widget
 * A cancas is a panel where we anchor widgets.
 * It generates tableless HTML and is used for absolute positioning.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Canvas extends P4A_Widget
{
	/**
	 * @var array
	 */
	protected $objects = array();
	
	/**
	 * @var integer
	 */
	protected $top = 10;
	
	/**
	 * @var integer
	 */
	protected $left = 10;
	
	/**
	 * @var string
	 */
	protected $unit = "px";
	
	/**
	 * @var integer
	 */
	protected $offset_top = 0;
	
	/**
	 * @var integer
	 */
	protected $offset_left = 0;

	/**
	 * @param P4A_Object $object
	 * @param integer $top
	 * @param integer $left
	 */
	public function anchor(&$object, $top, $left=0)
	{
		$this->objects[] = array($object, $top, $left);
	}

	/**
	 * @param integer $top
	 * @param integer $left
	 */
	public function setOffset($top, $left)
	{
		$this->offset_top += $top;
		$this->offset_left += $left;
	}

	/**
	 * @param integer $top
	 * @param integer $left
	 * @param string $unit
	 */
	public function defineGrid($top = 10, $left = 10, $unit = 'px')
	{
		$this->top = $top;
		$this->left = $top;
		$this->unit = $unit;
	}

	/**
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}
		
		$string  = "";
		foreach (array_keys($this->objects) as $key) {
			if (is_object($this->objects[$key][0])) {
				$top = ($this->objects[$key][1] * $this->top) + $this->offset_top;
				$left = ($this->objects[$key][2] * $this->left) + $this->offset_left;
				$unit = $this->unit;

				$string .= "<div id='$id' style='position:absolute;top:{$top}{$unit};left:{$left}{$unit};'>\n";
				$string .= $this->objects[$key][0]->getAsString() . "\n";
				$string .= "</div>\n\n";
				unset($object);
			}
		}
		return $string;
	}
}