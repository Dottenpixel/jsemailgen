<?php

header('Content-Type:text/html; charset=UTF-8');

$filename = "emaildat/$_GET[_c].dat";

if( is_readable($filename) ) {
	// Let's make sure the file exists and is writable first.
	if ($handle = fopen($filename, 'r')) {

		//$contents = fread( $handle );
	    
		$contents = file_get_contents( $filename );
		echo $contents;

	    fclose($handle);
	}
    
} else {
    echo "The file $filename is not readable";
    echo "Cannot open file ($filename)";
}

?>