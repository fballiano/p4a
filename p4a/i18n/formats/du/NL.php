<?

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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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

$numbers_formats = array
(
	"integer" => array('0', ',', '.'),
	"decimal" => array('2', ',', '.'),
	"float"   => array('*', ',', '.')
);

$datetime_formats = array
(
	"date_default"	=>	"%d-%m-%Y",
	"date_medium"	=>	"%d %b %Y",
	"date_long"		=>	"%d %B %Y",
	"date_full"		=>	"%A, %d %B %Y",
	
	"time_default"	=>	"%H:%M",
	"time_long"		=>	"%H:%M:%S"
);

$currency_formats = array
(
	"local"         => array('&euro; %', '2', ',', '.'),
	"international" => array('% EUR'   , '2', ',', '.')
);

?>