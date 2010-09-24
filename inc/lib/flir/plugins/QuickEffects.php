<?php
// JavaScript Document

/*
QuickEffects v0.1

Facelift was written and is maintained by Cory Mawhorter.  
It is available from http://facelift.mawhorter.net/

===

This file is part of Facelife Image Replacement ("FLIR").

FLIR is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FLIR is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/


$PLUGIN_ERROR = false;
define('IM_EXEC_PATH', '');
define('FULL_CACHE_PATH', getcwd().'/'.$cache);

$image = false;

$bounds = convertBoundingBox(imagettfbbox($size, 0, $font, $text));
$fulltrim = '';
if($FStyle['realFontHeight']!='true') {
	$REAL_HEIGHT_BOUNDS = $bounds;	
	$fulltrim = '-trim +repage';
}
	
$fore_hex = dec2hex($color['red'], $color['green'], $color['blue']);
$bkg_hex = dec2hex(abs($color['red']-100), abs($color['green']-100), abs($color['blue']-100));
$dpi = preg_match('#^[0-9]+$#', $FStyle['dpi']) ? $FStyle['dpi'] : 96;
$size = $size + ($size-((72/$dpi)*$size));
$out_width  = $bounds['width']+200;
$out_height = $REAL_HEIGHT_BOUNDS['height'];

$stroke = '';
if(isset($FStyle['qe_Stroke'])) {
	list($strokewidth, $strokecolor) = explode(',', $FStyle['qe_Stroke'], 2);
	$strokewidth=trim($strokewidth);
	$strokecolor=trim($strokecolor);
	$stroke = '-strokewidth '.(is_number($strokewidth)?$strokewidth:1).' -stroke "#'.(is_hexcolor($strokecolor)?strtoupper($strokecolor):'FF0000').'" ';
	
	$out_width+= ($strokewidth*2);
	$out_height+= ($strokewidth*2);
}

if(isset($FStyle['qe_Fill'])) {
	list($fill_type, $fill_options) = explode(',', $FStyle['qe_Fill'], 2);
	switch($fill_type) {
		case 'pattern':
			$cmds = array(IM_EXEC_PATH.'convert -size '.$out_width.'x'.$out_height.' -channel RGBA xc:"#'.$bkg_hex.'00" -font "'.$font.'" -pointsize '.$size.' -tile "'.getcwd().'/'.$fill_options.'" -gravity Center '.$stroke.' -annotate 0 "'.addcslashes($text, '"').'" '.$fulltrim.' "'.FULL_CACHE_PATH.'"');
			break;
		default:
		case 'gradient':
			list($color1, $color2) = explode(',', $fill_options, 2);
			$color1 = is_hexcolor($color1)?strtoupper($color1):'999999';
			$color2 = is_hexcolor($color2)?strtoupper($color2):'000000';
			$cmds = array(IM_EXEC_PATH.'convert -size '.$out_width.'x'.$out_height.' -channel RGBA xc:"#'.$bkg_hex.'00" -font "'.$font.'" -pointsize '.$size.' -tile gradient:"#'.$color1.'"-"#'.$color2.'" -gravity Center '.$stroke.' -annotate 0 "'.addcslashes($text, '"').'" '.$fulltrim.' "'.FULL_CACHE_PATH.'"');
			break;
	}
}else {
	$cmds = array(IM_EXEC_PATH.'convert -size '.$out_width.'x'.$out_height.' -background "#'.$bkg_hex.'00" -channel RGBA -fill "#'.$fore_hex.'" -font "'.$font.'" -pointsize '.$size.' -gravity Center '.$stroke.' caption:"'.addcslashes($text, '"').'" '.$fulltrim.' "'.FULL_CACHE_PATH.'"');
}

// SHADOW
if(isset($FStyle['qe_Shadow'])) {
	switch($FStyle['qe_Shadow']) {
		case 'high':
			$shadow = array('opacity' 		=> 55
								,'sigma' 		=> 3
								,'offset-left' => '+2'
								,'offset-top' 	=> '+2');
			break;
		case 'low':
			$shadow = array('opacity' 		=> 65
								,'sigma' 		=> 2
								,'offset-left' => '+2'
								,'offset-top' 	=> '+2');
			break;
		case 'fuzzy':
			$shadow = array('opacity' 		=> 55
								,'sigma' 		=> 4
								,'offset-left' => '+0'
								,'offset-top' 	=> '+0');
			break;
		/*
		case 'hard':
			$shadow = array('opacity' 		=> 75
								,'sigma' 		=> 0
								,'offset-left' => '+1'
								,'offset-top' 	=> '+1');
			break;
		case 'perspective':
			$shadow = array('opacity' 		=> 75
								,'sigma' 		=> 0
								,'offset-left' => '+1'
								,'offset-top' 	=> '+1');
			break;
		*/
		
		default:
			list($shadow_opac, $shadow_sig, $shadow_ol, $shadow_ot) = explode(',', $FStyle['qe_Shadow'], 4);
			$shadow_opac = (is_number($shadow_opac) && $shadow_opac<=100) ? $shadow_opac : 75;
			$shadow_sig = is_number($shadow_sig)?$shadow_sig:2;
			$shadow_ol = preg_match('#^[+-][0-9]{1,4}$#', $shadow_ol)?$shadow_ol:'+2';
			$shadow_ot = preg_match('#^[+-][0-9]{1,4}$#', $shadow_ot)?$shadow_ot:'+2';
			$shadow = array('opacity' 		=> $shadow_opac
								,'sigma' 		=> $shadow_sig
								,'offset-left' => $shadow_ol
								,'offset-top' 	=> $shadow_ot);
			break;
	}
						
	$cmds[] = IM_EXEC_PATH.'convert "'.FULL_CACHE_PATH.'" -matte ( +clone -background "#'.bkg_hex.'" -shadow '.$shadow['opacity'].'x'.$shadow['sigma'].$shadow['offset-left'].'x-5 ) +swap -background none -mosaic "'.FULL_CACHE_PATH.'"';
}

/*
header('Content-encoding: utf-8');
header('Content-type: text/plain');
foreach($cmds as $cmd)
	echo $cmd."\n";
exit;
*/

$cmds[] = IM_EXEC_PATH.'convert "'.FULL_CACHE_PATH.'" -matte -background none -flatten '.$fulltrim.' "'.FULL_CACHE_PATH.'"';


foreach($cmds as $cmd)
	exec($cmd);



if($FStyle['realFontHeight']=='true') { // trim sides
	/*
		 [0] => PNG 207x71 274x113+4+32
		 [1] => 207
		 [2] => 71
		 [3] => 274
		 [4] => 113
		 [5] => +4
		 [6] => +32
	*/
	$info = shell_exec(IM_EXEC_PATH.'convert "'.FULL_CACHE_PATH.'" -trim info:');
	preg_match('#PNG ([0-9]+)x([0-9]+) ([0-9]+)x([0-9]+)([+-][0-9]+)([+-][0-9]+)#', $info, $m);
//	print_r($m);
	exec(IM_EXEC_PATH.'convert "'.FULL_CACHE_PATH.'" -crop '.$m[1].'x'.$m[4].$m[5].'+0 +repage "'.FULL_CACHE_PATH.'"');
}

?>