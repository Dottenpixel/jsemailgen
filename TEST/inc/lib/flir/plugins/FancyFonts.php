<?php
// JavaScript Document

/*
FancyFonts v0.2

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

if($FStyle['ff_BlurEdges']=='true')
	$size+=2; // + 2 is to compensate for the blur we're adding

$cmd = IM_EXEC_PATH.'convert -size '.($bounds['width']+200).'x'.$REAL_HEIGHT_BOUNDS['height'].' -background none -channel RGBA -fill "#'.$fore_hex.'" -font "'.$font.'" -pointsize '.$size.' -gravity center'.' caption:"'.addcslashes($text, '"').'" -flatten '.$fulltrim.' "'.FULL_CACHE_PATH.'"';


/*
header('Content-encoding: utf-8');
header('Content-type: text/plain');
die($cmd);
*/

exec($cmd);

if($FStyle['ff_BlurEdges']=='true') {
	$cmd2 = IM_EXEC_PATH.'convert "'.FULL_CACHE_PATH.'" -matte -virtual-pixel transparent -channel A -blur 0x0.4  -level 0,50%  "'.FULL_CACHE_PATH.'"';	
//	exec($cmd2);
}


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
	
	exec(IM_EXEC_PATH.'convert "'.FULL_CACHE_PATH.'" -crop '.$m[1].'x'.$m[4].$m[5].'+0 +repage "'.FULL_CACHE_PATH.'"');
}
?>