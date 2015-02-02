<?php

class DateFormatter extends CDateFormatter {
	
	private $_locale;

	/**
	 * @var array pattern strings 
	 */
	private $_leerer_timestamp = array(
		'strich'	=> '-',
		'heute'		=> 'heute',
		'aktuell'	=> 'aktuell',
		'unbekannt'	=> 'unbekannt',
	);
	
	public function __construct() {
		$locale = Yii::app()->language;
		if(is_string($locale))
			$this->_locale=CLocale::getInstance($locale);
		else
			$this->_locale=$locale;
			
		parent::__construct($locale);
	}
	
	public function init() {}
	
	/**
	 * @param timestamp $timestamp
	 * @param string 	$dateWidth
	 * @param string 	$timeWidth
	 * @param boolean 	$aktuell
	 * @param string 	$leerer_timestamp
	 */
	public function formatDateTime($timestamp,$dateWidth='medium',$timeWidth='medium', $aktuell = false, $leerer_timestamp = null) {
		// Ausnahmenbehandlung für leere Timestamps
		if($timestamp == '0000-00-00') {
			return '-';
		}
		
		if ($aktuell) {
			return $this->_leerer_timestamp['aktuell'];
		} elseif (!$timestamp && array_key_exists($leerer_timestamp, $this->_leerer_timestamp)) {
			return $this->_leerer_timestamp[$leerer_timestamp];
		}
		return parent::formatDateTime($timestamp, $dateWidth, $timeWidth);
	}
	
	public function formatDateTimeAnzeige($timestamp,$dateWidth='medium',$timeWidth='medium', $dateTimeSeperator = false, $uhr = false) {
		$datum = '';
		if($dateTimeSeperator) {
			$datum.= parent::formatDateTime($timestamp,$dateWidth,false);						
			$datum.= $dateTimeSeperator;
			$datum.= parent::formatDateTime($timestamp,false,$timeWidth);
		}
		if($uhr) {
			$datum.= ' '.Yii::t('global','uhr');
		}		
		return $datum;
	}

	/**
	 * @param $datum (YYYY-MM-DD HH:ii:ss)
	 * @param string $format (full|long|medium|short)
	 */
	public function formatDatumOhneTimestamp($datum, $format = 'medium', $leeresDatum = 'heute') {
		/* Gültige Formate:
		 *     ["dateFormats"]=>
				    array(4) {
				      ["full"]		=> "EEEE, d. MMMM y"
				      ["long"]		=> "d. MMMM y"
				      ["medium"]	=> "dd.MM.yyyy"
				      ["short"]		=> "dd.MM.yy"
				    }
		 */
		if (substr($datum, 0, 10) == '0000-00-00') {
			return $this->_leerer_timestamp[$leeresDatum];
		} else {
			$date = $this->getDateFor($datum);
						
			$datePattern = $this->_locale->getDateFormat($format);
			return $this->formatiereDateTime($date, $datePattern);
		}
	}
	
	public function formatDateTimeOhneTimestamp($datum, $dateFormat = 'medium', $timeFormat = 'short') {
			$date = $this->getDateFor($datum);

			if (!empty($dateFormat)) {
				$datePattern = $this->_locale->getDateFormat($dateFormat);
				$dateAusgabe = $this->formatiereDateTime($date, $datePattern);
			}
			if (!empty($timeFormat)) {
				$timePattern = $this->_locale->getTimeFormat($timeFormat);
				$timeAusgabe = $this->formatiereDateTime($date, $timePattern);
			}
			
			if(isset($dateAusgabe) && isset($timeAusgabe)) {
				$dateTimePattern=$this->_locale->getDateTimeFormat();
				return strtr($dateTimePattern,array('{0}'=>$timeAusgabe,'{1}'=>$dateAusgabe));
			} elseif(isset($dateAusgabe)) {
				return $dateAusgabe;
			} elseif(isset($timeAusgabe)) {
				return $timeAusgabe;
			}
	}
	
	private function formatiereDateTime($date, $pattern) {
			$tokens = $this->parseFormat($pattern);
			foreach($tokens as &$token)
			{
				if(is_array($token)) // a callback: method name, sub-pattern
					$token = $this->{$token[0]}($token[1],$date);
			}
			return implode('',$tokens);
	}
	
	private function getDateFor($datum) {
		$dateTime = new DateTime($datum);
	
		$date = array(
			"seconds" => $dateTime->format('s'),
			"minutes" 	=> $dateTime->format('i'),
			"hours"  	=> $dateTime->format('G'),
			"mday"		=> $dateTime->format('j'),
			"wday"		=> $dateTime->format('N'),
			"mon"		=> $dateTime->format('n'),
			"year"		=> $dateTime->format('Y'),
			"yday"		=> $dateTime->format('z'),
			"weekday"	=> $dateTime->format('l'),
			"month"		=> $dateTime->format('F'),
			'0'			=> 0);
		return $date;
	}
	
	public function formatDayInWeek($pattern,$date)	{
		return parent::formatDayInWeek($pattern,$date);
	}
}