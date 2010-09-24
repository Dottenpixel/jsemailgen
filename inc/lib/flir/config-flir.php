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

define('UNKNOWN_FONT_SIZE', 		16); // in pixels

define('CACHE_CLEANUP_FREQ', 		-1); // -1 disable, 1 everytime, 10 would be about 1 in 10 times this script runs (higher number decreases frequency)
define('CACHE_KEEP_TIME', 			604800); // 604800: 7 days

define('CACHE_DIR', 					'cache');
define('FONTS_DIR', 					'fonts');
define('PLUGIN_DIR',					'plugins');

define('HBOUNDS_TEXT', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'); // see http://facelift.mawhorter.net/docs/

// Each font you want to use should have an entry in the fonts array.
$fonts = array();
$fonts['din'] 	= 'dinma___.ttf';

// The font will default to the following (put your most common font here).
$fonts['default'] 		= $fonts['din'];

/*
// You could also simply setup fonts to substitute the fonts that already exist.  
// This way you don't have to change your stylesheets at all.
$fonts['arial'] 				= your font;
$fonts['times new roman'] 	= your font;
$fonts['courier new'] 		= your font;
*/

define('DEBUG', false);
?>