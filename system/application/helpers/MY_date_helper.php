<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


/**
 * Timespan
 *
 * Returns a span of seconds in this format:
 *	10 days 14 hours 36 minutes 47 seconds
 *
 * @access	public
 * @param	integer	a number of seconds
 * @param	integer	Unix timestamp
 * @return	integer
 */	
if ( ! function_exists('timespan'))
{
	function timespan($time, $opt = array()) 
	{
		
		if(($time + 31556926) < time()) {
			
			// Return full date.
			return date('M j, Y g:i a', $time);
			
		} elseif(($time + 604800) < time()) {
			
			// Return month and day then time.
			return date('M j g:i a', $time);
			
		} elseif(($time + 3600*12) < time()) {
			
			// Return Day of the week and time
			return date('D g:i a', $time);
			
		} else {
			
			// The default values
			$defOptions = array(
				'to' => 0,
				'parts' => 1,
				'precision' => 'second',
				'distance' => true,
				'separator' => ' '
			);
			$opt = array_merge($defOptions, $opt);
			// Default to current time if no to point is given
			(!$opt['to']) && ($opt['to'] = time());
			// Init an empty string
			$str = '';
			// To or From computation
			$diff = ($opt['to'] > $time) ? $opt['to']-$time : $time-$opt['to'];
			// An array of label => periods of seconds;
			$periods = array(
				'decade' => 315569260,
				'year' => 31556926,
				'month' => 2629744,
				'week' => 604800,
				'day' => 86400,
				'hour' => 3600,
				'minute' => 60,
				'second' => 1
			);
			// Round to precision
			if ($opt['precision'] != 'second')
				$diff = round(($diff/$periods[$opt['precision']])) * $periods[$opt['precision']];
			// Report the value is 'less than 1 ' precision period away
			(0 == $diff) && ($str = 'less than 1 '.$opt['precision']);
			// Loop over each period
			foreach ($periods as $label => $value) {
				// Stitch together the time difference string
				(($x=floor($diff/$value))&&$opt['parts']--) && $str.=($str?$opt['separator']:'').($x.' '.$label.($x>1?'s':''));
				// Stop processing if no more parts are going to be reported.
				if ($opt['parts'] == 0 || $label == $opt['precision']) break;
				// Get ready for the next pass
				$diff -= $x*$value;
			}
			$opt['distance'] && $str.=($str&&$opt['to']>$time)?' ago':' away';
			return ($str == 'less than 1 second away') ? 'just now' : $str;
		}
	}
}
?>