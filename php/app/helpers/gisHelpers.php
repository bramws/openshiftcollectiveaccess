<?php
/** ---------------------------------------------------------------------
 * app/helpers/gisHelpers.php : GIS/mapping utility  functions
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 * 
 * @package CollectiveAccess
 * @subpackage utils
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */

 /**
   *
   */
    require_once(__CA_LIB_DIR__.'/ca/Attributes/Values/LengthAttributeValue.php');
    
 	/**
 	 * Converts $ps_value from degrees minutes seconds format to decimal
 	 */
	function caGISminutesToSignedDecimal($ps_value){
		$ps_value = trim($ps_value);
		$vs_value = preg_replace('/[^0-9A-Za-z\.\-]+/', ' ', $ps_value);
		
		if ($vs_value === $ps_value) { return $ps_value; }
		list($vn_deg, $vn_min, $vn_sec, $vs_dir) = explode(' ',$vs_value);
		$vn_pos = ($vn_deg < 0) ? -1:1;
		if (in_array(strtoupper($vs_dir), array('S', 'W'))) { $vn_pos = -1; }
		
		$vn_deg = abs(round($vn_deg,6));
		$vn_min = abs(round($vn_min,6));
		$vn_sec = abs(round($vn_sec,6));
		return round($vn_deg+($vn_min/60)+($vn_sec/3600),6)*$vn_pos;
	}
	
	/**
 	 * Converts $ps_value from decimal with N/S/E/W to signed decimal
 	 */
	function caGISDecimalToSignedDecimal($ps_value){
		$ps_value = trim($ps_value);
		list($vn_left_of_decimal, $vn_right_of_decimal, $vs_dir) = preg_split('![\. ]{1}!',$ps_value);
		if (preg_match('!([A-Za-z]+)$!', $vn_right_of_decimal, $va_matches)) {
			$vs_dir = $va_matches[1];
			$vn_right_of_decimal = preg_replace('!([A-Za-z]+)$!', '', $vn_right_of_decimal);
		}
		$vn_pos = 1;
		if (in_array(strtoupper($vs_dir), array('S', 'W'))) { $vn_pos = -1; }
		
		return floatval($vn_left_of_decimal.'.'.$vn_right_of_decimal) * $vn_pos;
	}
	
	/**
	 * Returns true if $ps_value is in degrees minutes seconds format
	 */ 
	function caGISisDMS($ps_value){
		if(preg_match('/[^0-9A-Za-z\.\- ]+/', $ps_value)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Parse map searches in the following formats:
	 *	[Box bounded by coordinates]
	 *		lat1,long1 to lat2,long2
	 *		lat1,long1 - lat2,long2
	 *		lat1,long1 .. lat2,long2
	 *			ex. 43.34,-74.24 .. 42.1,-75.02
	 *	
	 *		lat1,long1 ~ distance
	 *			ex. 43.34,-74.23 ~ 5km
	 *	[Area with
	 */ 
	function caParseGISSearch($ps_value){
		$ps_value = preg_replace('![ ]*,[ ]*!', ',', $ps_value);
		$ps_value = str_replace(" - ", " .. ", $ps_value);
		$ps_value = str_replace(" to ", " .. ", $ps_value);
		$ps_value = preg_replace('![^A-Za-z0-9,\.\-~ ]+!', '', $ps_value);
		
		$va_tokens = preg_split('![ ]+!', $ps_value);
		
		$vn_lat1 = $vn_long1 = $vn_lat2 = $vn_long2 = null;
		$vn_dist = null;
		$vn_state = 0;
		while(sizeof($va_tokens)) {
			$vs_token = trim(array_shift($va_tokens));
			switch($vn_state) {
				case 0:		// start
					$va_tmp = explode(',', $vs_token);
					if (sizeof($va_tmp) != 2) { return false; }
					$vn_lat1 = (float)$va_tmp[0];
					$vn_long1 = (float)$va_tmp[1];
					
					if (!sizeof($va_tokens)) {
						return array(
							'min_latitude' => $vn_lat1,
							'max_latitude' =>  $vn_lat1,
							'min_longitude' =>  $vn_long1,
							'max_longitude' =>  $vn_long1
						);
					}
					
					$vn_state = 1;
					break;
				case 1:		// conjunction
					switch($vs_token) {
						case '~':
							$vn_state = 3;
							break(2);
						case '..' :
							$vn_state = 2;
							break(2);
						default:
							$vn_state = 2;
							break;
					}
					// fall through
				case 2:	// second lat/long
					$va_tmp = explode(',', $vs_token);
					if (sizeof($va_tmp) != 2) { return false; }
					$vn_lat2 = (float)$va_tmp[0];
					$vn_long2 = (float)$va_tmp[1];
					
					if (($vn_lat1 == 0) || ($vn_lat2 == 0) || ($vn_long1 == 0) || ($vn_long2 == 0)) { return null; }
					
					return array(
						'min_latitude' => ($vn_lat1 > $vn_lat2) ? $vn_lat2 : $vn_lat1,
						'max_latitude' =>  ($vn_lat1 < $vn_lat2) ? $vn_lat2 : $vn_lat1,
						'min_longitude' =>  ($vn_long1 > $vn_long2) ? $vn_long2 : $vn_long1,
						'max_longitude' =>  ($vn_long1 < $vn_long2) ? $vn_long2 : $vn_long1,
					);
					break;
				case 3:	// distance
					//
					// TODO: The lat/long delta calculations below are very rough. We should replace with more accurate formulas.
					//
					$t_length = new LengthAttributeValue();
					$va_length_val = $t_length->parseValue($vs_token, array('displayLabel' => 'distance'));
					$vn_length = ((float)array_shift(explode(' ', preg_replace('![^\d\.]+!', '', $va_length_val['value_decimal1'])))) / 1000;		// kilometers
					$vn_lat1_km = (10000/90) * $vn_lat1;
					$vn_long1_km = (10000/90) * $vn_long1;
					
					$vn_lat1 = (($vn_lat1_km + ($vn_length/2)))/(10000/90);
					$vn_long1 = (($vn_long1_km + ($vn_length/2)))/(10000/90);
					
					$vn_lat2 = (($vn_lat1_km - ($vn_length/2)))/(10000/90);
					$vn_long2 = (($vn_long1_km - ($vn_length/2)))/(10000/90);
					
					if (($vn_lat1 == 0) || ($vn_lat2 == 0) || ($vn_long1 == 0) || ($vn_long2 == 0)) { return null; }
					
					return array(
						'min_latitude' => ($vn_lat1 > $vn_lat2) ? $vn_lat2 : $vn_lat1,
						'max_latitude' =>  ($vn_lat1 < $vn_lat2) ? $vn_lat2 : $vn_lat1,
						'min_longitude' =>  ($vn_long1 > $vn_long2) ? $vn_long2 : $vn_long1,
						'max_longitude' =>  ($vn_long1 < $vn_long2) ? $vn_long2 : $vn_long1,
					);
					
					break;
				
			}
		}
		
		return false;
	}
?>