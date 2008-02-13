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
 * General errors mask.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */
class P4A_Mask_Error extends P4A_Mask
{
	public function __construct()
	{
		parent::__construct();
		$p4a =& P4A::singleton();

		$title = $p4a->getTitle();
		if (strlen($title) > 0) {
			$title .= ': ';
		}

		$title .= 'FATAL ERROR';
		$this->setTitle($title);

		$this->build("P4A_Message", "message");
		$this->message->setIcon("error");
		$this->message->setWidth(300);

		$this->build("p4a_sheet", "sheet");
		$cell =& $this->sheet->anchor($this->message);
		$cell->setProperty("align", "center");

		$this->anchorRestart();

		if (P4A_EXTENDED_ERRORS) {
			$line = new p4a_line('line');
			$line->setWidth(90, '%');
			$this->sheet->anchor($line);

			$cell =& $this->sheet->anchorText('<b>Additional informations</b>');
			$cell->setProperty('align', 'center');
			unset($cell);

   			$this->build(P4A_FIELD_CLASS,"external_object");
   			$this->external_object->setInvisible();
   			$this->external_object->setType('label');
   			$this->external_object->setProperty('class', 'background1');
   			$this->sheet->anchor($this->external_object);

   			$this->build(P4A_FIELD_CLASS, "backtrace");
   			$this->backtrace->setType('label');
   			$this->sheet->anchor($this->backtrace);

   			$this->anchorRestart();
		}

		$this->display("main", $this->sheet);
	}

	function main($e)
	{
		$this->message->setValue($e->getMessage());

		if (P4A_EXTENDED_ERRORS) {
			$external_object = $e->getExternalObject();
			if ($external_object !== NULL) {
				$this->external_object->setVisible();
   				ob_start();
   				print_r($e->getExternalObject());
   				$external_object = preg_replace("/ /", '&nbsp;', ob_get_contents());
   				ob_end_clean();
   				$this->external_object->setValue($external_object);
			}

   			$backtrace = '';
   			foreach($e->getBacktrace() as $value) {
   				$backtrace .= '<table width="100%">';
   				foreach($value as $key=>$description) {
   					if (is_object($description) or is_resource($description)) {
						continue;
					}

   					$backtrace .= "<tr><td class='background2' width='1'><b>" .  ucfirst($key) . "</b></td><td class='background1' width='100%'>";
					if (is_array($description)) {
						ob_start();
						print_r($description);
   						$backtrace .= nl2br(preg_replace("/ /", '&nbsp;', ob_get_contents()));
   						ob_end_clean();
   					} elseif (is_string($description)) {
   						$backtrace .= $description;
   					}
   					$backtrace .= "</td></tr>";
   				}
   				$backtrace .= '</table><br>';
   			}

   			$this->backtrace->setValue($backtrace);
		}
		parent::main();
	}

	function anchorRestart()
	{
		$this->sheet->blankRow();
		$cell =& $this->sheet->anchorText('<a class="link" href="' . P4A_APPLICATION_PATH . '">Click here to restart the application</a>');
		$cell->setProperty('align', 'center');
		unset($cell);
	}
}