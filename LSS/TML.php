<?php
/**
 *  OpenLSS - Lighter Smarter Simpler
 *
 *	This file is part of OpenLSS.
 *
 *	OpenLSS is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Lesser General Public License as
 *	published by the Free Software Foundation, either version 3 of
 *	the License, or (at your option) any later version.
 *
 *	OpenLSS is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Lesser General Public License for more details.
 *
 *	You should have received a copy of the 
 *	GNU Lesser General Public License along with OpenLSS.
 *	If not, see <http://www.gnu.org/licenses/>.
 */
namespace LSS;

define('TML_DELIMITER','\t');
define('ERR_TML_PARSE_FAILED',1100);

//example map file (TAB DELIMITED)
/*
var
	name	value
	name	value
	name
		value
		value
		value
		name
			value
			value
			name	value
			name
				value
				value
			value
		value
	name	value
*/

class TML {

	public static function fromArray($arr,$level=0,$newline=true){
		$buf = '';
		foreach($arr as $name=>$value){
			if(is_array($value) && count($value)) $value = self::fromArray($value,($level+1),false);
			elseif(is_array($value) && !count($value)) $value = "\t[]";
			else $value = "\t".$value;
			$buf .= "\n".str_repeat("\t",$level).$name.$value;
		}
		if($newline) $buf = ltrim($buf)."\n";
		return $buf;
	}

	public static function toArray($buf){
		$lines = explode("\n",$buf);
		//first line has to be the var name (root)
		$varname = rtrim(trim(array_shift($lines)));
		if(empty($varname)) throw new Exception('Root missing, parse failed',ERR_TML_PARSE_FAILED);
		$map = array();
		if(self::lineParse($map,$lines))
			return array($varname => $map);
		throw new Exception('Parse failed',ERR_TML_PARSE_FAILED);
	}

	private static function lineParse(&$map,&$lines,$level=1){
		$m = array();
		while(($line = array_shift($lines)) !== false){
			//try for name value first
			if(preg_match("/^".TML_DELIMITER."{".$level."}(.+?)".TML_DELIMITER."+(.+?)$/",$line,$m)){
				if($m[2] == "[]") $map[$m[1]] = array();
				else $map[$m[1]] = $m[2];
				continue;
			}
			//try for name only with sublevel
			if(
				isset($lines[0])
				&& preg_match("/^".TML_DELIMITER."{".$level."}(.+?)\n".TML_DELIMITER."{".($level+1)."}(.+?)$/s",$line."\n".$lines[0],$m)
			){
				$map[$m[1]] = array();
				self::lineParse($map[$m[1]],$lines,($level+1));
				continue;
			}
			//try for name only no sublevel
			if(preg_match("/^".TML_DELIMITER."{".$level."}(.+?)$/",$line,$m)){
				$map[] = $m[1];
				continue;
			}
			if($level > 0) array_unshift($lines,$line);
			else throw new Exception('Parse would have never finished',ERR_TML_PARSE_FAILED);
			return true;
		}
		return true;
	}

}
