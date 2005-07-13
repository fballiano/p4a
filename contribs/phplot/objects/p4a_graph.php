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
 * This widet draws graphs
 * @author Luca Rossi <Major__Tom@libero.it>
 */
class P4A_Graph extends P4A_Widget
{
		/**
		 * Phplot graph object.
		 * @var phplot
		 * @access private
		 */
		var $graph;
		
		/**
		 * Array data for chart.
		 * @var mixed array
		 * @access private
		 */
		var $graphData = array();

		var $height = 400;
		var $width = 400;
		var $title = "p4a graph";
		var $type = "bars";
		var $xlbl = "X axis";
		var $ylbl = "Y axis";
		var $dt = "text-data";
		var $errorBarLineWidth ;
		var $fileFormat = "PNG";
		var $ttf = NULL;
		var $bgColor = NULL;
		var $gridColor = NULL;
		var $legendVisible = false;
		var $legendCols = array();
		var $legPixX = NULL;
		var $legPixY = NULL;
		
		var $dataCols = array();
		var $labelCol = NULL;
		
		var $data = NULL;
		
		var $rows = NULL;
		var $cols = array();
	
		function &P4A_Graph($name)
		{
			parent::P4A_Widget($name);
		}
		
		/**
		 * Sets mode for fill graph
		 * @param mode	-- can be "manual" or "db", some methods are unavailable in manual mode
		 * @access public
		 * @see $mode
		 */
		function setMode($mode)
		{
			$this->mode= $mode;
		}
		
		/**
		 * Sets the data source for the graph.
		 * this method is available only in db mode
		 * @param data_source		The data source.
		 * @access public
		 */
		function setSource(&$data_source)
		{
				unset($this->data);
				$this->data =& $data_source;
		}
		
		function addDataCol($colName, $legendName)
		{
			array_push($this->dataCols, $colName);
			array_push($this->legendCols, $legendName);
		}
		
		function setLabelCol($colName)
		{
			$this->labelCol = $colName;
		}
		
		
		function setHeight($height)
		{
			$this->height = $height;
		}
		
		function setWidth($width)
		{
			$this->width = $width;
		}
		
		/*
         * text-data: ('label', y1, y2, y3, ...)
         * text-data-single: ('label', data), for some pie charts.
         * data-data: ('label', x, y1, y2, y3, ...)
         * data-data-error: ('label', x1, y1, e1+, e2-, y2, e2+, e2-, y3, e3+, e3-, ...)
         */
		function setDataType($which_dt)
		{
			$this->dt->$which_dt;
		}
		
		// User Function: Can be: bars, lines, linepoints, area, points, and pie
		
		/**
		 * Set the type of the graph
		 * Can be: bars, lines, linepoints, area, points, and pie
		 * @param string $type.
		 * @access public
		 */
		function setPlotType($type)
		{
			$this->type = $type;
		}
		
		// Width of the Error Bars in Pixels. If not set then uses "line_width" to set the width of the error_bar lines.
		function SetErrorBarLineWidth($width)
		{
			$this->errorBarLineWidth = $width;
		}
		
		
        //User Function: Set the format of the output graph. Supported formats are GIF, JPEG, and PNG. You can only use those for
        //mats that are supported by your version of GD. For example, if you use GD-1.8.3 you can not use GIF images. If you use GD		
        //-1.2 you can not use PNG or JPEG images.
    	function SetFileFormat($which_file_format)
    	{
    		$this->fileFormat = $which_file_format;
    	}
    	
    	// User Function: Call this as SetUseTTF(1) when you have TTF compiled into PHP otherwise call this as SetUseTTF(0)	
    	function SetUseTTF($wich_ttf)
    	{
    		$this->ttf = $wich_ttf;
    	}
   		 
   		/**
		 * Set the color of the background of the entire image.
		 * $which_color can be either a name like "black" or an rgb color array array(int,int,int).
		 * It defaults to array(222,222,222) if not defined.
		 * @access public
		 * @params which_color
		 */ 
    	function SetBackgroundColor($which_color)
    	{
    		$this->bgColor = $which_color;
    	}
    	
    	/**
		 * Set the color of the grid.
		 * Defaults to "black" $which_color can be either a name like "black" 
		 * or an rgb color array array(int,int,int).
		 * @access public
		 * @params which_color
		 */ 
    	function SetGridColor ($which_color)
    	{
    		$this->gridColor = $wich_color;
    	}
		
		function setLegendVisible($visible)
		{
			$this->legendVisible = $visible;
		}
    	
    	/**
		 * Pick the upper left corner of the legend box with $which_x and $which_y in pixels. 
		 * $which_type is reserved for future use.
		 * @access public
		 * @params $which_x
		 * @params $which_y
		 * @params $which_type
		 */
    	function SetLegendPixels($which_x,$which_y,$which_type=-1)
    	{
    		$this->legPixX = $which_x;
    		$this->legPixY = $which_y;
    	}

    	
    	/**
		 * Set the graph title. 
		 * @params string title
		 */
		function setTitle($title)
		{
			$this->title = $title;
		}
    
    	/**
		 * Set the X axis label. 
		 * @params string xlbl
		 */
		function SetXLabel($xlbl)
		{
			$this->xlbl = $xlbl;
		}
    	
    	/**
		 * Set the Y axis label. 
		 * @params string ylbl
		 */
		function SetYLabel($ylbl)
		{
			$this->ylbl = $ylbl;
		}
   
		/**
		 * Add a data row to the graph. 
		 * @params array data
		 */
		function addData($data)
		{
			array_push($this->graphData, $data);
		}
	
		function getAsString()
		{
			$this->fillGraph();
			
			if (!$this->isVisible()) {
				return '';
			}
			
			if (count($this->graphData)>0) {
				$id = $this->getId();
				$string = '<img src="phplot_wrapper.php?id='.$id.'&p4a_application_name='.P4A_APPLICATION_NAME.'&p4a_root_dir='.P4A_ROOT_DIR.'" width="'.$this->width.'" height="'.$this->height.'">';
			} else {
				$string = '<b>Please Initialize Data</b>';
			}
			return $string;
		}	
		
		//
		function fillGraph()
		{
			// populate the graph
			unset($this->graphData);
			$this->graphData = array();
			
			$rows = $this->data->getAll();
			foreach ($rows as $row) {
				//$this->addData(array($row[$this->labelCol], $row[$this->dataCol], $row['value2']));
				$colStr = $row[$this->labelCol];
				foreach ($this->dataCols as $col) {
					$colStr .= ",{$row[$col]}";
				}
				$this->addData(explode(",", $colStr));
			}
		}
		
 		function display()
		{
			require "phplot/phplot.php"
			$this->graph = new PHPlot($this->width, $this->height);
			$this->graph->setDataType($this->dt);
			$this->graph->SetErrorBarLineWidth($this->errorBarLineWidth);
			$this->graph->SetFileFormat($this->fileFormat);		
			
			if ($this->ttf <> NULL) {
				$this->graph->SetUseTTF($this->ttf);
			}	
			
			if ($this->bgColor <> NULL) {
				$this->graph->SetBackgroundColor($this->bgColor);
			}	

			if ($this->gridColor <> NULL) {
				$this->graph->SetGridColor($this->gridColor);
			}	
			
			if ($this->legendVisible == true) {
				$this->graph->SetLegend($this->legendCols);
			}		
			
			if ($this->legPixX <> NULL) {
				$this->graph->SetLegendPixel($this->legPixX, $this->legPixY, NULL);
			}
			
			$this->graph->setTitle($this->title);
			$this->graph->SetXLabel($this->xlbl);
			$this->graph->SetYLabel($this->ylbl);
			$this->graph->setPlotType($this->type);
			$this->graph->setDataValues($this->graphData);
			$this->graph->SetXDataLabelPos("plotdown");
			
			return $this->graph->DrawGraph();
		} 				
}