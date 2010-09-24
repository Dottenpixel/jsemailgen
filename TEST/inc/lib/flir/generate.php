<?php
/*
Facelift Image Replacement v1.1.1

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
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require('config-flir.php');
require('inc-flir.php');

$fonts_dir = str_replace('\\', '/', realpath(FONTS_DIR.'/'));

if(substr($fonts_dir, -1) != '/')
	$fonts_dir .= '/';

$FStyle = preg_match('#^\{("[\w]+":"[^"]*",?)*\}$#i', $_GET['fstyle'])?json_decode($_GET['fstyle'], true):array();

$mode		= isset($FStyle['mode']) ? $FStyle['mode'] : 'progressive';

$extSize = isset($FStyle['fixedSize']) ? $FStyle['fixedSize'] : $FStyle['cssSize'];
$size 	= is_number($extSize) ? $extSize : UNKNOWN_FONT_SIZE; // pixels
$maxheight= is_number($_GET['h']) ? (int)$_GET['h'] : UNKNOWN_FONT_SIZE; // pixels
$maxwidth= is_number($_GET['w']) ? (int)$_GET['w'] : 800; // pixels
$font 	= $fonts_dir.(isset($fonts[$FStyle['cssFont']]) ? $fonts[$FStyle['cssFont']] : $fonts['default']); // "tw-conden-bold.ttf";

$color = array();
if(preg_match('#(\(([0-9]{1,3}), ?([0-9]{1,3}), ?([0-9]{1,3})\))#i', $FStyle['cssColor'], $m)) {
	$color['red'] 		= $m[2];
	$color['green'] 	= $m[3];
	$color['blue'] 	= $m[4];
}elseif(is_hexcolor($FStyle['cssColor'])) {
	$colors 	= explode(',',substr(chunk_split($FStyle['cssColor'], 2, ','), 0, -1));
	$color['red'] 		= hexdec($colors[0]);
	$color['green'] 	= hexdec($colors[1]);
	$color['blue'] 	= hexdec($colors[2]);
}else {
	$color = css2hex($FStyle['cssColor']);
}

function replace_unicode($m) {
	return chr(base_convert(substr($m[0], 1),16,10));
}

$text 	= isset($_GET['text'])?strip_tags($_GET['text']):'unknown';
$text		= preg_replace_callback('#\%u[a-f0-9]{4}#i', 'replace_unicode', $text);
$cache 	= get_cache_fn(md5($font.(print_r($FStyle,true).$text)));

$text = str_replace('{amp}', '&', $text);
$text = html_entity_decode($text, ENT_QUOTES, 'utf-8');

if(file_exists($cache) && !DEBUG) {
	output_file($cache);
}else {	
	$REAL_HEIGHT_BOUNDS = $FStyle['realFontHeight']=='true' ? convertBoundingBox(imagettfbbox($size, 0, $font, HBOUNDS_TEXT)) : false;
	
	switch($mode) {
		default:
			$dir = dir(PLUGIN_DIR);
			$php_mode = strtolower($mode.'.php');
			while(false !== ($entry = $dir->read())) {
				$p = PLUGIN_DIR.'/'.$entry;
				if(is_dir($p) || $entry == '.' || $entry == '..') continue;
				
				if($php_mode == strtolower($entry)) {
					$dir->close();
					$PLUGIN_ERROR = false;
					
					try {
						include($p);
					}catch(Exception $err) {
						$PLUGIN_ERROR = true;
					}
					
					if(false !== $PLUGIN_ERROR)
						break;
					else
						break(2);
				}
			}
			$dir->close();
		
			$bounds = convertBoundingBox(imagettfbbox($size, 0, $font, $text));  // second argument '0' is angle of text
			if($FStyle['realFontHeight']!='true') 
				$REAL_HEIGHT_BOUNDS = $bounds;
				
			
			$image = imagecreatetruecolor($bounds['width'], $REAL_HEIGHT_BOUNDS['height']);
			imagesavealpha($image, true);
			imagealphablending($image, false);
	
			imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), imagecolorallocatealpha($image, abs($color['red']-100), abs($color['green']-100), abs($color['blue']-100), 127));
			
			imagettftext($image, $size, 0, $REAL_HEIGHT_BOUNDS['xOffset'], $REAL_HEIGHT_BOUNDS['yOffset'], imagecolorallocate($image, $color['red'], $color['green'], $color['blue']), $font, $text);
			break;
		case 'wrap':
			$bounds = convertBoundingBox(imagettfbbox($size, 0, $font, $text));  // second argument '0' is angle of text
			if($FStyle['realFontHeight']!='true') 
				$REAL_HEIGHT_BOUNDS = $bounds;
	
			// if mode is wrap, check to see if text needs to be wrapped, otherwise let continue to progressive
			if($bounds['width'] > $maxwidth) {
				$image = imagettftextbox($size, 0, 0, 0, $color, $font, $text, $maxwidth, $FStyle['cssAlign']);
				break;
			}else {
				$image = imagecreatetruecolor($bounds['width'], $REAL_HEIGHT_BOUNDS['height']);
				imagesavealpha($image, true);
				imagealphablending($image, false);
		
				imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), imagecolorallocatealpha($image, abs($color['red']-100), abs($color['green']-100), abs($color['blue']-100), 127));
				
				imagettftext($image, $size, 0, $REAL_HEIGHT_BOUNDS['xOffset'], $REAL_HEIGHT_BOUNDS['yOffset'], imagecolorallocate($image, $color['red'], $color['green'], $color['blue']), $font, $text);
			}
			break;
		case 'progressive':
			$bounds = convertBoundingBox(imagettfbbox($size, 0, $font, $text));  // second argument '0' is angle of text
			if($FStyle['realFontHeight']!='true') 
				$REAL_HEIGHT_BOUNDS = $bounds;
			
			$offset_left = 0;
			
			$nsize=$size;
			while(($REAL_HEIGHT_BOUNDS['height'] > $maxheight || $bounds['width'] > $maxwidth) && $nsize > 2) {
				$nsize--;
				$bounds = convertBoundingBox(imagettfbbox($nsize, 0, $font, $text));
				$REAL_HEIGHT_BOUNDS = $FStyle['realFontHeight']=='true' ? convertBoundingBox(imagettfbbox($nsize, 0, $font, HBOUNDS_TEXT)) : $bounds;
			}
			$size = $nsize;
	
			$image = imagecreatetruecolor($bounds['width'], $REAL_HEIGHT_BOUNDS['height']);
			imagesavealpha($image, true);
			imagealphablending($image, false);
	
			imagefilledrectangle($image, $offset_left, 0, imagesx($image), imagesy($image), imagecolorallocatealpha($image, abs($color['red']-100), abs($color['green']-100), abs($color['blue']-100), 127));
			
			imagettftext($image, $size, 0, $REAL_HEIGHT_BOUNDS['xOffset'], $REAL_HEIGHT_BOUNDS['yOffset'], imagecolorallocate($image, $color['red'], $color['green'], $color['blue']), $font, $text);
			break;
	}
	
	if(false !== $image) {
		imagepng($image, $cache);
		imagedestroy($image);
	}
	
	output_file($cache);
} // if(file_exists($cache)) {

flush();

if(CACHE_CLEANUP_FREQ != -1 && rand(1, CACHE_CLEANUP_FREQ) == 1)
	@cleanup_cache();
?>