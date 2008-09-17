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
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

/**
 * General errors mask.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
final class P4A_Error_Mask extends P4A_Mask
{
	/**
	 * @var P4A_Box
	 */
	protected $box = null;
	
	/**
	 * @var P4A_Frame
	 */
	public $frame = null;
	
	/**
	 * @var P4A_Button
	 */
	public $restart_button = null;

	public function __construct()
	{
		parent::__construct();
		$this->setTitle("Error");
		
		$this->build("P4A_Frame", "frame");
		
		$this->build("P4A_Box", "box")
			->setStyleProperty("border", "1px solid #c6d3de")
			->setStyleProperty("padding", "10px");
		
		$this->build("P4A_Button", "restart_button")
			->setLabel("restart application")
			->addAction("onclick");
		
		$this->frame
			->anchor($this->box)
			->newRow()
			->anchorCenter($this->restart_button);
		
		$this->display("main", $this->frame);
	}
	
	public function main()
	{
		parent::main();
		P4A::singleton()->close();
	}

	/**
	 * @param string $html
	 * @return P4A_Error_Mask
	 */
	public function setMessage($html)
	{
		$this->box->setHTML($html);
		return $this;
	}
}