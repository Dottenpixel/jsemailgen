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
if(DEBUG) {
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
}

/***
 *
 * Can be deleted if magic quotes is disabled.  Magic quotes, what a plague it is/was.
 *
*/
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

function get_cache_fn($md5) {
	$tier1 = CACHE_DIR.'/'.$md5[0].$md5[1];
	$tier2 = $tier1.'/'.$md5[2].$md5[3];
	
	if(!file_exists($tier1))
		mkdir($tier1);
	if(!file_exists($tier2))
		mkdir($tier2);
		
	return $tier2.'/'.$md5.'.png';
}

function cleanup_cache() {
	$d1 = dir(CACHE_DIR);
	while(false !== ($tier1 = $d1->read())) {
		if($tier1 == '.' || $tier1 == '..') continue; 
		
		$d2 = dir(CACHE_DIR.'/'.$tier1);
		while(false !== ($tier2 = $d2->read())) {
			if($tier2 == '.' || $tier2 == '..') continue; 
			
			$path = CACHE_DIR.'/'.$tier1.'/'.$tier2;
			$d3 = dir($path);
			while(false !== ($entry = $d3->read())) {
				if($entry == '.' || $entry == '..') continue; 
				
				if((time() - filectime($path.'/'.$entry)) > CACHE_KEEP_TIME) {
//					echo $path.'/'.$entry.' removed<BR>';
					unlink($path.'/'.$entry);
				}
			}
			$d3->close();			
		}
		$d2->close();
	}
	$d1->close();
}

function imagettftextbox($size, $angle, $left, $top, $color, $font, $raw_text, $max_width, $align="left") {
	$raw_textlines = explode("\n", $raw_text);
	
	$formatted_lines = $formatted_widths = array();
	$max_leftoffset = $max_rightoffset = $max_baseheight = $max_lineheight = 0;
	
	foreach($raw_textlines as $text) {		
		$bounds = convertBoundingBox(imagettfbbox($size, $angle, $font, $text));
		if($bounds['height'] > $max_lineheight)
			$max_lineheight = $bounds['height'];
		if($bounds['belowBasepoint'] > $max_baseheight)
			$max_baseheight = $bounds['belowBasepoint'];
		if($bounds['xOffset'] > $max_leftoffset)
			$max_leftoffset = $bounds['xOffset'];
		if($bounds['yOffset'] > $max_rightoffset)
			$max_rightoffset = $bounds['yOffset'];

		if($bounds['width'] < $max_width) { // text doesn't require wrapping
			$formatted_lines[] = $text;
			$formatted_widths[$text] = $bounds['width'];
		}else { // text requires wrapping
			$words = explode(' ', trim($text));
			
			$test_line = '';
			for($i=0; $i < count($words); $i++) { // test words one-by-one to see if they put the width over
				$prepend = $i==0 ? '' : $test_line.' '; // add space if not the first word
				$working_line = $prepend.$words[$i];
				
				$bounds = convertBoundingBox(imagettfbbox($size, $angle, $font, $working_line)); // test working line
				
//				echo $working_line." - ".$bounds['width']."<BR>";
				
				if($bounds['width'] > $max_width) { // if working line is too big previous line isn't, use that 
//					echo '<strong>too big, adding previous "'.$test_line.'"</strong><br>';
					$formatted_lines[] = $test_line;
					$formatted_widths[$test_line] = $previous_bounds['width'];
					$test_line = $words[$i];
				}else { // keep adding
					$test_line = $working_line;
				}
				
				$previous_bounds = $bounds;
			}
			
			if($test_line!='') { // if words are finished and there is something left in the buffer add it
				$bounds = convertBoundingBox(imagettfbbox($size, $angle, $font, $test_line)); // test working line

//				echo '<strong>ended, adding line "'.$test_line.'"</strong><br>';
				$formatted_lines[] = $test_line;
				$formatted_widths[$test_line] = $bounds['width'];
			}
		}
		// The standard Witness pistol is steel designed to help control felt%0Arecoil, as well as provide...
	}
	
//	print_r($formatted_widths);
//	exit;
	
	$image = imagecreatetruecolor($max_width, ($max_lineheight*count($formatted_lines))+$max_baseheight);
	imagesavealpha($image, true);
	imagealphablending($image, false);
	
	imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), imagecolorallocatealpha($image, abs($color['red']-100), abs($color['green']-100), abs($color['blue']-100), 127));	
	
	for($i=0; $i < count($formatted_lines); $i++) {
		$offset_top = ($max_lineheight*($i+1))+$top;

		switch(strtolower($align)) {
			default:
			case 'left':
				$offset_left = $left;
				break;
			case 'center':
				$offset_left = ($max_width-$formatted_widths[$formatted_lines[$i]])/2;
				break;
			case 'right':
				$offset_left = ($max_width-$formatted_widths[$formatted_lines[$i]])-5;
				break;
		}

		imagettftext($image, $size, $angle, $offset_left, $offset_top, imagecolorallocate($image, $color['red'], $color['green'], $color['blue']), $font, $formatted_lines[$i]);
	}
	
	return $image;
}

function css2hex($css_str) {
	$css_color = array();
	
	$css_color['aliceblue']  	  	= 'f0f8ff';
	$css_color['antiquewhite']  	  	= 'faebd7';
	$css_color['aqua']  	  	= '00ffff';
	$css_color['aquamarine']  	  	= '7fffd4';
	$css_color['azure']  	  	= 'f0ffff';
	$css_color['beige']  	  	= 'f5f5dc';
	$css_color['bisque']  	  	= 'ffe4c4';
	$css_color['black']  	  	= '000000';
	$css_color['blanchedalmond']  	  	= 'ffebcd';
	$css_color['blue']  	  	= '0000ff';
	$css_color['blueviolet']  	  	= '8a2be2';
	$css_color['brown']  	  	= 'a52a2a';
	$css_color['burlywood']  	  	= 'deb887';
	$css_color['cadetblue']  	  	= '5f9ea0';
	$css_color['chartreuse']  	  	= '7fff00';
	$css_color['chocolate']  	  	= 'd2691e';
	$css_color['coral']  	  	= 'ff7f50';
	$css_color['cornflowerblue']  	  	= '6495ed';
	$css_color['cornsilk']  	  	= 'fff8dc';
	$css_color['crimson']  	  	= 'dc143c';
	$css_color['cyan']  	  	= '00ffff';
	$css_color['darkblue']  	  	= '00008b';
	$css_color['darkcyan']  	  	= '008b8b';
	$css_color['darkgoldenrod']  	  	= 'b8860b';
	$css_color['darkgray']  	  	= 'a9a9a9';
	$css_color['darkgrey']  	  	= 'a9a9a9';
	$css_color['darkgreen']  	  	= '006400';
	$css_color['darkkhaki']  	  	= 'bdb76b';
	$css_color['darkmagenta']  	  	= '8b008b';
	$css_color['darkolivegreen']  	  	= '556b2f';
	$css_color['darkorange']  	  	= 'ff8c00';
	$css_color['darkorchid']  	  	= '9932cc';
	$css_color['darkred']  	  	= '8b0000';
	$css_color['darksalmon']  	  	= 'e9967a';
	$css_color['darkseagreen']  	  	= '8fbc8f';
	$css_color['darkslateblue']  	  	= '483d8b';
	$css_color['darkslategray']  	  	= '2f4f4f';
	$css_color['darkslategrey']  	  	= '2f4f4f';
	$css_color['darkturquoise']  	  	= '00ced1';
	$css_color['darkviolet']  	  	= '9400d3';
	$css_color['deeppink']  	  	= 'ff1493';
	$css_color['deepskyblue']  	  	= '00bfff';
	$css_color['dimgray']  	  	= '696969';
	$css_color['dimgrey']  	  	= '696969';
	$css_color['dodgerblue']  	  	= '1e90ff';
	$css_color['firebrick']  	  	= 'b22222';
	$css_color['floralwhite']  	  	= 'fffaf0';
	$css_color['forestgreen']  	  	= '228b22';
	$css_color['fuchsia']  	  	= 'ff00ff';
	$css_color['gainsboro']  	  	= 'dcdcdc';
	$css_color['ghostwhite']  	  	= 'f8f8ff';
	$css_color['gold']  	  	= 'ffd700';
	$css_color['goldenrod']  	  	= 'daa520';
	$css_color['gray']  	  	= '808080';
	$css_color['grey']  	  	= '808080';
	$css_color['green']  	  	= '008000';
	$css_color['greenyellow']  	  	= 'adff2f';
	$css_color['honeydew']  	  	= 'f0fff0';
	$css_color['hotpink']  	  	= 'ff69b4';
	$css_color['indianred']  	  	= 'cd5c5c';
	$css_color['indigo']  	  	= '4b0082';
	$css_color['ivory']  	  	= 'fffff0';
	$css_color['khaki']  	  	= 'f0e68c';
	$css_color['lavender']  	  	= 'e6e6fa';
	$css_color['lavenderblush']  	  	= 'fff0f5';
	$css_color['lawngreen']  	  	= '7cfc00';
	$css_color['lemonchiffon']  	  	= 'fffacd';
	$css_color['lightblue']  	  	= 'add8e6';
	$css_color['lightcoral']  	  	= 'f08080';
	$css_color['lightcyan']  	  	= 'e0ffff';
	$css_color['lightgoldenrodyellow']  	  	= 'fafad2';
	$css_color['lightgray']  	  	= 'd3d3d3';
	$css_color['lightgrey']  	  	= 'd3d3d3';
	$css_color['lightgreen']  	  	= '90ee90';
	$css_color['lightpink']  	  	= 'ffb6c1';
	$css_color['lightsalmon']  	  	= 'ffa07a';
	$css_color['lightseagreen']  	  	= '20b2aa';
	$css_color['lightskyblue']  	  	= '87cefa';
	$css_color['lightslategray']  	  	= '778899';
	$css_color['lightslategrey']  	  	= '778899';
	$css_color['lightsteelblue']  	  	= 'b0c4de';
	$css_color['lightyellow']  	  	= 'ffffe0';
	$css_color['lime']  	  	= '00ff00';
	$css_color['limegreen']  	  	= '32cd32';
	$css_color['linen']  	  	= 'faf0e6';
	$css_color['magenta']  	  	= 'ff00ff';
	$css_color['maroon']  	  	= '800000';
	$css_color['mediumaquamarine']  	  	= '66cdaa';
	$css_color['mediumblue']  	  	= '0000cd';
	$css_color['mediumorchid']  	  	= 'ba55d3';
	$css_color['mediumpurple']  	  	= '9370d8';
	$css_color['mediumseagreen']  	  	= '3cb371';
	$css_color['mediumslateblue']  	  	= '7b68ee';
	$css_color['mediumspringgreen']  	  	= '00fa9a';
	$css_color['mediumturquoise']  	  	= '48d1cc';
	$css_color['mediumvioletred']  	  	= 'c71585';
	$css_color['midnightblue']  	  	= '191970';
	$css_color['mintcream']  	  	= 'f5fffa';
	$css_color['mistyrose']  	  	= 'ffe4e1';
	$css_color['moccasin']  	  	= 'ffe4b5';
	$css_color['navajowhite']  	  	= 'ffdead';
	$css_color['navy']  	  	= '000080';
	$css_color['oldlace']  	  	= 'fdf5e6';
	$css_color['olive']  	  	= '808000';
	$css_color['olivedrab']  	  	= '6b8e23';
	$css_color['orange']  	  	= 'ffa500';
	$css_color['orangered']  	  	= 'ff4500';
	$css_color['orchid']  	  	= 'da70d6';
	$css_color['palegoldenrod']  	  	= 'eee8aa';
	$css_color['palegreen']  	  	= '98fb98';
	$css_color['paleturquoise']  	  	= 'afeeee';
	$css_color['palevioletred']  	  	= 'd87093';
	$css_color['papayawhip']  	  	= 'ffefd5';
	$css_color['peachpuff']  	  	= 'ffdab9';
	$css_color['peru']  	  	= 'cd853f';
	$css_color['pink']  	  	= 'ffc0cb';
	$css_color['plum']  	  	= 'dda0dd';
	$css_color['powderblue']  	  	= 'b0e0e6';
	$css_color['purple']  	  	= '800080';
	$css_color['red']  	  	= 'ff0000';
	$css_color['rosybrown']  	  	= 'bc8f8f';
	$css_color['royalblue']  	  	= '4169e1';
	$css_color['saddlebrown']  	  	= '8b4513';
	$css_color['salmon']  	  	= 'fa8072';
	$css_color['sandybrown']  	  	= 'f4a460';
	$css_color['seagreen']  	  	= '2e8b57';
	$css_color['seashell']  	  	= 'fff5ee';
	$css_color['sienna']  	  	= 'a0522d';
	$css_color['silver']  	  	= 'c0c0c0';
	$css_color['skyblue']  	  	= '87ceeb';
	$css_color['slateblue']  	  	= '6a5acd';
	$css_color['slategray']  	  	= '708090';
	$css_color['slategrey']  	  	= '708090';
	$css_color['snow']  	  	= 'fffafa';
	$css_color['springgreen']  	  	= '00ff7f';
	$css_color['steelblue']  	  	= '4682b4';
	$css_color['tan']  	  	= 'd2b48c';
	$css_color['teal']  	  	= '008080';
	$css_color['thistle']  	  	= 'd8bfd8';
	$css_color['tomato']  	  	= 'ff6347';
	$css_color['turquoise']  	  	= '40e0d0';
	$css_color['violet']  	  	= 'ee82ee';
	$css_color['wheat']  	  	= 'f5deb3';
	$css_color['white']  	  	= 'ffffff';
	$css_color['whitesmoke']  	  	= 'f5f5f5';
	$css_color['yellow']  	  	= 'ffff00';
	$css_color['yellowgreen']  	  	= '9acd32';

	$color = isset($css_color[$css_str])?$css_color[$css_str]:'000000';
	$colors 	= explode(',',substr(chunk_split($color, 2, ','), 0, -1));
	$acolor = array();
	$acolor['red'] 	= hexdec($colors[0]);
	$acolor['green'] 	= hexdec($colors[1]);
	$acolor['blue'] 	= hexdec($colors[2]);
	
	return $acolor;
}

function dec2hex($r, $g, $b) {
	$hxr = dechex($r);
	$hxg = dechex($g);
	$hxb = dechex($b);
	
	return strtoupper((strlen($hxr)==1?'0'.$hxr:$hxr).(strlen($hxg)==1?'0'.$hxg:$hxg).(strlen($hxb)==1?'0'.$hxb:$hxb));
}

function output_file($cache_file) {
	$ts = filemtime($cache_file);

	$ifmodsince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])?$_SERVER['HTTP_IF_MODIFIED_SINCE']:false;
	if ($ifmodsince && strtotime($ifmodsince) >= $ts) {
		header('HTTP/1.0 304 Not Modified', true, 304);
		return;
	}
	
	$etag = isset($_SERVER['HTTP_IF_NONE-MATCH'])?$_SERVER['HTTP_IF_NONE-MATCH']:false;
	if($etag && $etag == md5($ts)) {
		header('HTTP/1.0 304 Not Modified', true, 304);
		return;
	}
	
	header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', $ts));
	header('ETag: "'.md5($ts).'"');

	header('Content-Type: image/png');
	readfile($cache_file);
//	exit;
}

/*
0  lower left corner, X position			-3
1 	lower left corner, Y position			10
2 	lower right corner, X position		735
3 	lower right corner, Y position		10
4 	upper right corner, X position		735
5 	upper right corner, Y position		-44
6 	upper left corner, X position			-3
7 	upper left corner, Y position			-44

$width = abs($bounds[2]) + abs($bounds[0]);
$height = abs($bounds[7]) + abs($bounds[1]);
*/	
function convertBoundingBox ($bbox) {
    if ($bbox[0] >= -1)
        $xOffset = -abs($bbox[0] + 1);
    else
        $xOffset = abs($bbox[0] + 2);
    $width = abs($bbox[2] - $bbox[0]);
    if ($bbox[0] < -1) $width = abs($bbox[2]) + abs($bbox[0]) - 1;
    $yOffset = abs($bbox[5] + 1);
    if ($bbox[5] >= -1) $yOffset = -$yOffset; // Fixed characters below the baseline.
    $height = abs($bbox[7]) - abs($bbox[1]);
    if ($bbox[3] > 0) $height = abs($bbox[7] - $bbox[1]) - 1;
    return array(
        'width' => $width,
        'height' => $height,
        'xOffset' => $xOffset, // Using xCoord + xOffset with imagettftext puts the left most pixel of the text at xCoord.
        'yOffset' => $yOffset, // Using yCoord + yOffset with imagettftext puts the top most pixel of the text at yCoord.
        'belowBasepoint' => max(0, $bbox[1])
    );
}

function is_number($str, $bAllowZero=false) {
	$regex = $bAllowZero?'[0-9]+':'[1-9][0-9]*';
	return preg_match('#^'.$regex.'$#', $str);
}

function is_hexcolor($str) {
	return preg_match('#^[a-f0-9]{6}$#i', $str);
}
?>