<?php

class Highchart
{
	var $chart = array();
	var $chart_type = '';
	public function __construct($type='')
	{
		//Here we setup some standard fields
		$this->chart = array();
		$this->chart['chart'] = array();
		$this->chart['chart']['backgroundColor'] = 'none';

		$this->chart['title'] = array();
		$this->chart['tooltip'] = array();
		$this->chart['credits'] = array();
		$this->chart['yAxis'] = array();
		//$this->chart['xAxis'] = array();



		$this->chart['series'] = array();

		$this->chart['credits']['enabled'] = false;
		$this->chart['legend']['enabled'] = false;
		$this->chart['tooltip']['shared'] = true;
		$this->chart['tooltip']['valueSuffix'] ='';
		$this->chart['title']['text'] = '';
		$this->chart['yAxis']['title'] = array();
		$this->chart['yAxis']['title']['text'] = '';



		//If type is set add some more standard settings
		if(!empty($type))
		{
			$this->chart_type = $type;
			$this->chart['chart']['type'] = $type;
			//$this->chart['plotOptions'][$type] = array();
		}
	}

	function correctData($data)
	{
		$newdata = array();
		foreach($data as $d)
		{
			if(empty($d))
			{
				array_push($newdata, null);
			}
			else {
				array_push($newdata, round(floatval($d),2));
			}
		}

		return $newdata;

	}

	public function setType($type)
	{
		$this->chart['chart']['type'] = $type;
		//$this->chart['plotOptions'][$type] = array();
		$this->chart_type = $type;
	}

	public function addPlotOption($option, $value)
	{
		if(!empty($this->chart_type))
		{
			if(!isset($this->chart['plotOptions']))
			{
				$this->chart['plotOptions'] = array();
			}
			if(!isset($this->chart['plotOptions'][$this->chart_type]))
			{
				$this->chart['plotOptions'][$this->chart_type] = array();
			}

			$this->chart['plotOptions'][$this->chart_type][$option] = $value;
		}
		else
		{
			return false;
		}
	}
	public function addTimestamps($start, $interval)
	{
		$this->chart['xAxis']['type'] = "datetime";
		$this->addPlotOption("pointInterval", $interval);
		$this->addPlotOption("pointStart",$start);

	}

	public function setTitle($title)
	{
		$this->chart['title']['text'] = $title;
	}

	public function setYAxisLabel($label)
	{
		$this->chart['yAxis']['title']['text'] = $label;
	}

	public function addLegend()
	{
		$this->chart['legend']['enabled'] = true;
		$this->chart['legend']['layout'] = 'horizontal';
		$this->chart['legend']['align'] = 'center';
		$this->chart['legend']['verticalAlign'] = 'top';
		$this->chart['legend']['floating'] = true;
		$this->chart['legend']['borderWidth'] = 0;
		$this->chart['legend']['backgroundColor'] = 'transparent';
	}

	public function disableTooltip()
	{
		$this->chart['tooltip']['enabled'] = false;
	}

	public function addCategories($cats, $step =1)
	{
		$this->chart['xAxis']["categories"] = $cats;
		if($step > 1)
		{
			$this->chart['xAxis']['labels'] = array();
			$this->chart['xAxis']['labels']['step'] = $step;
		}
	}

	public function addPlotBand($from, $to, $color)
	{
		if(is_null($this->chart['xAxis']['plotBands']))
		{
			$this->chart['xAxis']['plotBands'] = array();
		}

		$newPlot = array();
		$newPlot['from'] = $from;
		$newPlot['to'] = $to;
		$newPlot['color'] = $color;

		array_push($this->chart['xAxis']['plotBands'], $newPlot);

	}

	public function addSeries($data, $name='', $color='', $options = array())
	{
		$newSeries = array();
		$newSeries['data'] = $this->correctData($data);
		if(!empty($name))
		{
		$newSeries['name'] = $name;
		}
		if(!empty($color))
		{
		$newSeries['color'] = $color;
		}
		foreach ($options as $key => $value)
		{
				$newSeries[$key] = $value;
		}

		array_push($this->chart['series'],$newSeries);
	}

	/* Requires Drilldown Module */
	public function addDrilldownSeries($labels, $data, $subdata, $name='', $color='')
	{
		$this->chart['xAxis']['type']= 'category';

		/* Create the series */

		$newSeries = array();

		if(!empty($name))
		{
			$newSeries['name'] = $name;
		}
		if(!empty($color))
		{
			$newSeries['color'] = $color;
		}

		$newSeries['data'] = array();

		$ddLabel = array();

		for($i = 0; $i < count($data); $i++)
		{
			$dataPoint = array();
			$dataPoint['name'] = $labels[$i];
			$dataPoint['y'] = $data[$i];
			$ddLabel[$i] = $labels[$i] . 'DD';
			$dataPoint['drilldown'] = $ddLabel[$i];
			array_push($newSeries['data'], $dataPoint);
		}

		array_push($this->chart['series'],$newSeries);
		/* Create the drilldowns */
		if(!isset($this->chart['drilldown']['series']))
		{
			$this->chart['drilldown']['series'] = array();
		}

		for($i = 0; $i < count($labels); $i++)
		{
			$label = $labels[$i];
			if(isset($subdata[$label]))
			{
				$ddSeries = array();
				$ddSeries['id'] = $ddLabel[$i];
				$ddSeries['data'] = array();
				$subd = $subdata[$label];
				foreach($subd as $k=>$v)
				{
					$ddData = array();
					$ddData[0] = $k;
					$ddData[1] = $v;
					array_push($ddSeries['data'], $ddData);
				}
				array_push($this->chart['drilldown']['series'], $ddSeries);
			}
		}



	}

	public function addDrilldown($seriesName, $data)
	{
		$ser = $this->chart['series'];
		$ns = count($ser);

		$ddS = -1;
		for($i = 0; $i < $ns; $i++)
		{

			if($ser[$i]['name'] == $seriesName)
			{
				$ddS = $i;
				break;
			}
		}

		if($ddS > -1)
		{
			$this->chart['series'][$ddS]['drilldown'] = "DD" . $seriesName;

			if(!isset($this->chart['drilldown']['series']))
			{
				$this->chart['drilldown']['series'] = array();
			}

			$newDD = array();
			$newDD['id'] = "DD" . $seriesName;
			$newDD['data'] = array();
			foreach($data as $k=>$v)
			{
				$dp = array();
				$dp[0] = $k;
				$dp[1] = $v;
				array_push($newDD['data'], $dp);
			}

			array_push($this->chart['drilldown']['series'],$newDD);
		}
	}


	/* Import / Export data functions */

	public function importArray()
	{

	}

	public function getArray()
	{
		return $this->chart;
	}
	public function toJSON()
	{
		return str_replace("},","},\n",json_encode($this->chart));
	}

	public function toChart($selector)
	{
		return "$('" . $selector . "').highcharts(\n" . $this->toJSON() . "\n);\n\n";
	}


}

class Highstock
{
	public function __construct()
	{
		$chart = array();

		$this->chart['title'] = array();

		$this->chart['title']['text'] = '';
		$this->chart['rangeSelector'] = array();
		$this->chart['rangeSelector']['selected'] = 1;
		$this->chart['credits']['enabled'] = false;
		$this->chart['series'] = array();
	}



		public function addSeries($time, $data, $name='', $color='')
	{
		$newSeries = array();

		if(!empty($name))
		{
		$newSeries['name'] = $name;
		}
		if(!empty($color))
		{
		$newSeries['color'] = $color;
		}

		$newSeries['type'] = 'spline';

		$newSeries['data'] = $this->correctData($time, $data);

		array_push($this->chart['series'],$newSeries);
	}
	function correctData($time, $data)
	{
		$i = 0;
		$newdata = array();
		foreach($data as $d)
		{
			$dt = strtotime($time[$i]) * 1000;
			array_push($newdata, array($dt, intval($d)));
			$i++;
		}

		return $newdata;

	}

		public function addLegend()
	{
		$this->chart['legend'] = array();
		//$this->chart['legend']['layout'] = 'vertical';
		$this->chart['legend']['align'] = 'center';
		//$this->chart['legend']['verticalAlign'] = 'top';
		//$this->chart['legend']['floating'] = true;
		$this->chart['legend']['borderWidth'] = 0;
		$this->chart['legend']['backgroundColor'] = 'white';
	}

	public function toJSON()
	{
		return str_replace("},","},\n",json_encode($this->chart));
	}

	public function toChart($selector)
	{
		return "$('" . $selector . "').highcharts('StockChart',\n" . $this->toJSON() . "\n);\n\n";
	}

}



?>
